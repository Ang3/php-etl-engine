<?php

namespace Ang3\Component\ETL\ErrorHandler;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\ErrorType;
use Ang3\Component\ETL\Contract\RowInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final readonly class EtlError
{
    public function __construct(
        public \Throwable $exception,
        public ErrorType $type,
        public ErrorStage $stage,
        public ?RowInterface $row = null,
        public ?FieldMetadata $field = null,
        public mixed $rawValue = null,
        public ?ContextInterface $context = null,
    ) {
    }

    public function getFieldReference(): ?string
    {
        return $this->field?->reference;
    }

    public function getMessage(): string
    {
        return $this->exception->getMessage();
    }

    public function isInputError(): bool
    {
        return $this->type === ErrorType::Input;
    }

    public function isInternalError(): bool
    {
        return $this->type === ErrorType::Internal;
    }
}