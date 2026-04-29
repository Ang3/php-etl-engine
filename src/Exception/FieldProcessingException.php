<?php

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Metadata\FieldMetadata;

class FieldProcessingException extends EtlStageException
{
    public function __construct(private readonly FieldMetadata $field,
                                private readonly mixed         $rawValue,
                                \Throwable                     $previous,
                                ?string $message = null)
    {
        parent::__construct(
            ErrorStage::Field,
            $message ?: sprintf('Error while transforming field "%s".', $field->reference),
            0,
            $previous
        );
    }

    public function getField(): FieldMetadata
    {
        return $this->field;
    }

    public function getRawValue(): mixed
    {
        return $this->rawValue;
    }
}