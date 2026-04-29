<?php

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Metadata\FieldMetadata;

final class MissingTransformerException extends FieldProcessingException implements TechnicalExceptionInterface
{
    public function __construct(FieldMetadata $field,
                                mixed         $rawValue,
                                \Throwable    $previous)
    {
        parent::__construct(
            $field,
            $rawValue,
            $previous,
            sprintf('No transformer found for field "%s" of type "%s".', $field->reference, $field->type),
        );
    }
}