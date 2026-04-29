<?php

namespace Ang3\Component\ETL\Pipeline;

use Ang3\Component\ETL\Contract\ContextFactoryInterface;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\ErrorStrategy;
use Ang3\Component\ETL\Contract\Enum\ErrorType;
use Ang3\Component\ETL\Contract\ErrorHandlerInterface;
use Ang3\Component\ETL\Contract\FieldProcessorInterface;
use Ang3\Component\ETL\Contract\OutputRowFactoryInterface;
use Ang3\Component\ETL\Contract\PipelineInterface;
use Ang3\Component\ETL\Contract\WriterInterface;
use Ang3\Component\ETL\ErrorHandler\EtlError;
use Ang3\Component\ETL\Exception\FieldProcessingException;
use Ang3\Component\ETL\Exception\ValidationExceptionInterface;

readonly class DefaultPipeline implements PipelineInterface
{
    public function __construct(
        private FieldProcessorInterface $fieldProcessor,
        private ContextFactoryInterface $contextFactory,
        private OutputRowFactoryInterface $outputRowFactory,
        private WriterInterface $writer,
        private ErrorHandlerInterface $errorHandler,
    ) {
    }

    public function process(DatasetInterface $dataset): void
    {
        $fields = $dataset->getFields();

        try {
            foreach ($dataset->getRows() as $row) {
                $record = $this->outputRowFactory->create();
                $context = $this->contextFactory->create($row, $record, $dataset);

                foreach ($fields as $field) {
                    try {
                        $this->fieldProcessor->process($field, $context);
                    } catch (\Throwable $exception) {
                        $errorStrategy = $this->errorHandler->handle(new EtlError(
                            exception: $exception,
                            type: $exception instanceof ValidationExceptionInterface ? ErrorType::Input : ErrorType::Internal,
                            stage: ErrorStage::Field,
                            row: $row,
                            field: $field,
                            rawValue: $exception instanceof FieldProcessingException ? $exception->getRawValue() : null,
                            context: $context,
                        ));

                        if (ErrorStrategy::Stop === $errorStrategy) {
                            throw $exception;
                        }
                    }
                }

                $this->writer->write($record, $context);
            }
        } catch (\Throwable $exception) {
            $this->errorHandler->handle(new EtlError(
                exception: $exception,
                type: ErrorType::Internal,
                stage: ErrorStage::Row,
            ));
        }
    }
}