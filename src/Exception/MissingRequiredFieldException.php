<?php

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Metadata\FieldMetadata;

final class MissingRequiredFieldException extends FieldProcessingException implements ValidationExceptionInterface
{
    public function __construct(FieldMetadata $field,
                                mixed         $rawValue,
                                \Throwable    $previous)
    {
        parent::__construct(
            $field,
            $rawValue,
            $previous,
            sprintf('Missing required field "%s".', $field->reference),
        );
    }
}