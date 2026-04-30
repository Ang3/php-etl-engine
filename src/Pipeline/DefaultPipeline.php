<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Pipeline;

use Ang3\Component\ETL\Contract\ContextFactoryInterface;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\Enum\RowStatus;
use Ang3\Component\ETL\Contract\FieldProcessorInterface;
use Ang3\Component\ETL\Contract\OutputRowFactoryInterface;
use Ang3\Component\ETL\Contract\PipelineInterface;
use Ang3\Component\ETL\Contract\WriterInterface;
use Ang3\Component\ETL\Result\ProcessedRow;

readonly class DefaultPipeline implements PipelineInterface
{
    public function __construct(
        private FieldProcessorInterface $fieldProcessor,
        private ContextFactoryInterface $contextFactory,
        private OutputRowFactoryInterface $outputRowFactory,
        private WriterInterface $writer,
    ) {
    }

    public function process(DatasetInterface $dataset): void
    {
        $fields = $dataset->getFields();

        foreach ($dataset->getRows() as $row) {
            $record = $this->outputRowFactory->create();
            $context = $this->contextFactory->create($dataset, $row, $record);

            foreach ($fields as $field) {
                $this->fieldProcessor->process($field, $context);
            }

            $this->writer->write(
                new ProcessedRow(
                    input: $row,
                    output: $record,
                    status: RowStatus::Valid,
                    rowIndex: $context->rowIndex(),
                    sourceLineNumber: $context->sourceLineNumber(),
                ),
                $context
            );
        }
    }
}
