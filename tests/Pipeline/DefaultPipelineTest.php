<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Pipeline;

use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Factory\DefaultContextFactory;
use Ang3\Component\ETL\Factory\DefaultErrorFactory;
use Ang3\Component\ETL\Factory\DefaultOutputRowFactory;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use Ang3\Component\ETL\Metadata\FieldTargetMetadata;
use Ang3\Component\ETL\Metadata\IndexedRowMetadata;
use Ang3\Component\ETL\Pipeline\DefaultPipeline;
use Ang3\Component\ETL\Processor\DefaultFieldProcessor;
use Ang3\Component\ETL\Registry\FieldTransformerRegistry;
use Ang3\Component\ETL\Resolver\DefaultFieldValueResolver;
use Ang3\Component\ETL\Resolver\DefaultHeaderResolver;
use Ang3\Component\ETL\Tests\Fixtures\ArrayDataset;
use Ang3\Component\ETL\Tests\Fixtures\CallableFieldTransformer;
use Ang3\Component\ETL\Tests\Fixtures\RecordingWriter;
use PHPUnit\Framework\TestCase;

final class DefaultPipelineTest extends TestCase
{
    public function testAllValidRowsAreWrittenAndCounted(): void
    {
        $writer = new RecordingWriter();
        $pipeline = $this->makePipeline($writer);
        $dataset = $this->makeDataset([
            new IndexedRowMetadata(['a' => 'ok'], 0),
            new IndexedRowMetadata(['a' => 'ok'], 1),
        ]);

        $report = $pipeline->process($dataset);

        self::assertSame(2, $report->processedRows());
        self::assertSame(2, $report->validRows());
        self::assertSame(0, $report->invalidRows());
        self::assertSame(2, $report->writtenRows());
        self::assertSame(0, $report->errorCount());
        self::assertFalse($report->hasFatalError());
        self::assertCount(2, $writer->written);
    }

    public function testFieldErrorsAreCollectedPerRowAndTheRowIsStillWritten(): void
    {
        $writer = new RecordingWriter();
        $pipeline = $this->makePipeline($writer);
        $dataset = $this->makeDataset([
            new IndexedRowMetadata(['a' => 'boom', 'b' => 'boom'], 0),
        ], fieldCount: 2);

        $report = $pipeline->process($dataset);

        self::assertSame(1, $report->processedRows());
        self::assertSame(0, $report->validRows());
        self::assertSame(1, $report->invalidRows());
        self::assertSame(1, $report->writtenRows());
        self::assertSame(2, $report->errorCount());
        self::assertCount(2, $report->sampleErrors());

        foreach ($report->sampleErrors() as $error) {
            // The transformer's exception is wrapped into a FieldProcessingException by
            // DefaultFieldProcessor, which reports its own precise stage via
            // StagedExceptionInterface — overriding the FieldProcessing fallback stage
            // DefaultPipeline passes to ErrorFactoryInterface::create().
            self::assertSame(ErrorStage::FieldTransformation, $error->stage);
        }

        self::assertCount(1, $writer->written);
        self::assertTrue($writer->written[0]->isInvalid());
    }

    public function testWriterFailureIsRecordedAndProcessingContinuesWhenPermissive(): void
    {
        $writer = new RecordingWriter(failing: true);
        $pipeline = $this->makePipeline($writer, strict: false);
        $dataset = $this->makeDataset([
            new IndexedRowMetadata(['a' => 'ok'], 0),
            new IndexedRowMetadata(['a' => 'ok'], 1),
        ]);

        $report = $pipeline->process($dataset);

        self::assertSame(2, $report->processedRows());
        self::assertSame(0, $report->writtenRows());
        self::assertSame(2, $report->errorCount());
        self::assertFalse($report->hasFatalError());

        foreach ($report->sampleErrors() as $error) {
            self::assertSame(ErrorStage::Writing, $error->stage);
        }
    }

    public function testWriterFailureStopsThePipelineWhenStrict(): void
    {
        $writer = new RecordingWriter(failing: true);
        $pipeline = $this->makePipeline($writer, strict: true);
        $dataset = $this->makeDataset([
            new IndexedRowMetadata(['a' => 'ok'], 0),
            new IndexedRowMetadata(['a' => 'ok'], 1),
        ]);

        $report = $pipeline->process($dataset);

        self::assertSame(1, $report->processedRows());
        self::assertTrue($report->hasFatalError());
        self::assertSame(ErrorStage::Writing, $report->fatalError()?->stage);
    }

    public function testDatasetRowIterationFailureIsFatal(): void
    {
        $writer = new RecordingWriter();
        $pipeline = $this->makePipeline($writer);
        $dataset = new class implements DatasetInterface {
            public function getFields(): array
            {
                return [];
            }

            public function getRows(): iterable
            {
                throw new \RuntimeException('dataset is broken');
            }
        };

        $report = $pipeline->process($dataset);

        self::assertSame(0, $report->processedRows());
        self::assertTrue($report->hasFatalError());
        self::assertSame(ErrorStage::Pipeline, $report->fatalError()?->stage);
        self::assertSame([], $writer->written);
    }

    public function testDatasetFieldsFailureIsFatal(): void
    {
        $writer = new RecordingWriter();
        $pipeline = $this->makePipeline($writer);
        $dataset = new class implements DatasetInterface {
            public function getFields(): array
            {
                throw new \RuntimeException('fields are broken');
            }

            public function getRows(): iterable
            {
                return [];
            }
        };

        $report = $pipeline->process($dataset);

        self::assertSame(0, $report->processedRows());
        self::assertTrue($report->hasFatalError());
        self::assertSame(ErrorStage::Pipeline, $report->fatalError()?->stage);
    }

    private function makePipeline(RecordingWriter $writer, bool $strict = false): DefaultPipeline
    {
        $registry = new FieldTransformerRegistry();
        $registry->add(new CallableFieldTransformer(static function (mixed $value): mixed {
            if ('boom' === $value) {
                throw new \RuntimeException('transform boom');
            }

            return $value;
        }));

        return new DefaultPipeline(
            contextFactory: new DefaultContextFactory(),
            fieldProcessor: new DefaultFieldProcessor(new DefaultFieldValueResolver(new DefaultHeaderResolver()), $registry),
            outputRowFactory: new DefaultOutputRowFactory(),
            writer: $writer,
            errorFactory: new DefaultErrorFactory(),
            strict: $strict,
        );
    }

    /**
     * @param list<IndexedRowMetadata> $rows
     */
    private function makeDataset(array $rows, int $fieldCount = 1): ArrayDataset
    {
        $references = ['a', 'b', 'c'];
        $fields = [];

        foreach ($references as $index => $reference) {
            if ($index >= $fieldCount) {
                break;
            }

            $fields[] = new FieldMetadata(reference: $reference, type: 'string', target: new FieldTargetMetadata('x', $reference));
        }

        return new ArrayDataset($fields, $rows);
    }
}
