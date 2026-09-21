<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Processor;

use Ang3\Component\ETL\Context\DefaultContext;
use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Exception\FieldProcessingException;
use Ang3\Component\ETL\Exception\MissingRequiredFieldValueException;
use Ang3\Component\ETL\Exception\MissingTransformerException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use Ang3\Component\ETL\Metadata\RowMetadata;
use Ang3\Component\ETL\Processor\DefaultFieldProcessor;
use Ang3\Component\ETL\Registry\FieldTransformerRegistry;
use Ang3\Component\ETL\Tests\Fixtures\CallableFieldTransformer;
use Ang3\Component\ETL\Tests\Fixtures\CallableFieldValueResolver;
use PHPUnit\Framework\TestCase;

final class DefaultFieldProcessorTest extends TestCase
{
    public function testResolvesTransformsAndWritesTheFieldValue(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $resolver = new CallableFieldValueResolver(static fn () => '12.5');
        $registry = new FieldTransformerRegistry();
        $registry->add(new CallableFieldTransformer(static fn (mixed $value) => is_numeric($value) ? (float) $value : 0.0));
        $processor = new DefaultFieldProcessor($resolver, $registry);
        $context = $this->makeContext();

        $processor->process($field, $context);

        self::assertSame(12.5, $context->getOutput('amount'));
    }

    public function testWrapsAnUnexpectedResolutionFailure(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $resolver = new CallableFieldValueResolver(static function (): mixed {
            throw new \RuntimeException('resolution boom');
        });
        $processor = new DefaultFieldProcessor($resolver, new FieldTransformerRegistry());

        try {
            $processor->process($field, $this->makeContext());
            self::fail('Expected a FieldProcessingException.');
        } catch (FieldProcessingException $e) {
            self::assertSame(ErrorStage::FieldResolution, $e->stage());
            self::assertSame($field, $e->field());
            self::assertInstanceOf(\RuntimeException::class, $e->getPrevious());
        }
    }

    public function testLeavesAnEtlExceptionRaisedDuringResolutionUntouched(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $resolver = new CallableFieldValueResolver(static function () use ($field): mixed {
            throw new MissingRequiredFieldValueException($field);
        });
        $processor = new DefaultFieldProcessor($resolver, new FieldTransformerRegistry());

        $this->expectException(MissingRequiredFieldValueException::class);

        $processor->process($field, $this->makeContext());
    }

    public function testLeavesTheMissingTransformerExceptionUntouched(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $resolver = new CallableFieldValueResolver(static fn () => 'value');
        $processor = new DefaultFieldProcessor($resolver, new FieldTransformerRegistry());

        $this->expectException(MissingTransformerException::class);

        $processor->process($field, $this->makeContext());
    }

    public function testWrapsAnUnexpectedTransformationFailure(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $resolver = new CallableFieldValueResolver(static fn () => 'value');
        $registry = new FieldTransformerRegistry();
        $registry->add(new CallableFieldTransformer(static function (): mixed {
            throw new \RuntimeException('transform boom');
        }));
        $processor = new DefaultFieldProcessor($resolver, $registry);

        try {
            $processor->process($field, $this->makeContext());
            self::fail('Expected a FieldProcessingException.');
        } catch (FieldProcessingException $e) {
            self::assertSame(ErrorStage::FieldTransformation, $e->stage());
            self::assertSame('value', $e->rawValue());
        }
    }

    private function makeContext(): ContextInterface
    {
        return new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(), new RowMetadata());
    }
}
