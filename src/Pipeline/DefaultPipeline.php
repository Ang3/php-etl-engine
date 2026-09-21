<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Pipeline;

use Ang3\Component\ETL\Contract\ContextFactoryInterface;
use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\ErrorType;
use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Contract\Enum\RowStatus;
use Ang3\Component\ETL\Contract\ErrorFactoryInterface;
use Ang3\Component\ETL\Contract\FieldProcessorInterface;
use Ang3\Component\ETL\Contract\OutputRowFactoryInterface;
use Ang3\Component\ETL\Contract\PipelineInterface;
use Ang3\Component\ETL\Contract\WriterInterface;
use Ang3\Component\ETL\Error\EtlError;
use Ang3\Component\ETL\Result\EtlReport;
use Ang3\Component\ETL\Result\ProcessedRow;

readonly class DefaultPipeline implements PipelineInterface
{
    /**
     * Context option controlling whether a writer failure aborts the whole pipeline.
     *
     * @see ContextInterface::getOption()
     */
    public const OPTION_STRICT_WRITE_ERRORS = 'strict_write_errors';

    public function __construct(
        private ContextFactoryInterface $contextFactory,
        private FieldProcessorInterface $fieldProcessor,
        private OutputRowFactoryInterface $outputRowFactory,
        private WriterInterface $writer,
        private ErrorFactoryInterface $errorFactory,
        private bool $strict = false,
    ) {
    }

    public function process(DatasetInterface $dataset): EtlReport
    {
        $report = new EtlReport();

        try {
            $this->doProcess($dataset, $report);
        } catch (\Throwable $e) {
            $report->markFatal(new EtlError(
                errorCode: EtlErrorCode::UnexpectedError,
                type: ErrorType::Technical,
                stage: ErrorStage::Pipeline,
                message: $e->getMessage(),
                errorParameters: ['exception' => $e::class],
                exceptionClass: $e::class,
            ));
        }

        return $report;
    }

    /**
     * @internal
     */
    private function doProcess(DatasetInterface $dataset, EtlReport $report): void
    {
        $fields = $dataset->getFields();

        foreach ($dataset->getRows() as $row) {
            $report->incrementProcessedRows();

            $record = $this->outputRowFactory->create();
            $context = $this->contextFactory->create($dataset, $row, $record);
            $context->setOption(self::OPTION_STRICT_WRITE_ERRORS, $this->strict);

            $rowErrors = $this->processFields($fields, $context, $report);
            $status = [] === $rowErrors ? RowStatus::Valid : RowStatus::Invalid;

            if (RowStatus::Valid === $status) {
                $report->incrementValidRows();
            } else {
                $report->incrementInvalidRows();
            }

            $processedRow = new ProcessedRow(
                input: $row,
                output: $record,
                status: $status,
                rowIndex: $context->rowIndex(),
                sourceLineNumber: $context->sourceLineNumber(),
                errors: $rowErrors,
            );

            try {
                $this->writer->write($processedRow, $context);
                $report->incrementWrittenRows();
            } catch (\Throwable $e) {
                $error = $this->errorFactory->create($e, ErrorStage::Writing, $context);

                if ($context->getOption(self::OPTION_STRICT_WRITE_ERRORS, $this->strict)) {
                    $report->markFatal($error);

                    return;
                }

                $report->addError($error);
            }
        }
    }

    /**
     * Processes every field of a row, collecting one error per failing field instead of failing fast.
     *
     * @param array<\Ang3\Component\ETL\Metadata\FieldMetadata> $fields
     *
     * @return list<EtlError>
     *
     * @internal
     */
    private function processFields(array $fields, ContextInterface $context, EtlReport $report): array
    {
        $errors = [];

        foreach ($fields as $field) {
            try {
                $this->fieldProcessor->process($field, $context);
            } catch (\Throwable $e) {
                $error = $this->errorFactory->create($e, ErrorStage::FieldProcessing, $context, $field);
                $errors[] = $error;
                $report->addError($error);
            }
        }

        return $errors;
    }
}
