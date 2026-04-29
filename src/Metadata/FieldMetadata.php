<?php

namespace Ang3\Component\ETL\Metadata;

class FieldMetadata
{
    public function __construct(
        public string  $reference,
        public string  $type,
        public string  $targetSubject,
        public string  $targetField,
        public ?string $sourceKey = null,
        public ?string $defaultValue = null,
        public ?array  $options = [],
    )
    {
    }
}
