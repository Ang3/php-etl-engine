<?php

namespace Ang3\Component\ETL\Contract;

use Ang3\Component\ETL\Metadata\FieldMetadata;

interface FieldValueResolverInterface
{
    public function resolve(FieldMetadata $field, ContextInterface $context): mixed;
}