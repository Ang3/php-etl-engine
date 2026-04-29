<?php

namespace Ang3\Component\ETL\Contract;

use Ang3\Component\ETL\Metadata\FieldMetadata;

interface FieldProcessorInterface
{
    public function process(FieldMetadata $field, ContextInterface $context): void;
}