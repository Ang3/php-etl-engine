<?php

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Metadata\FieldMetadata;

final class InvalidFieldValueException extends FieldProcessingException implements ValidationExceptionInterface
{
    public function __construct(FieldMetadata $field,
                                mixed         $rawValue,
                                \Throwable    $previous)
    {
        parent::__construct(
            $field,
            $rawValue,
            $previous,
            sprintf('The value of the field "%s" is not valid.', $field->reference),
        );
    }
}