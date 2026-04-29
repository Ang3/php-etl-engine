<?php

namespace Ang3\Component\ETL\Contract;

use Ang3\Component\ETL\Metadata\FieldMetadata;

interface FieldTransformerInterface
{
    public function supports(FieldMetadata $field): bool;

    public function transform(mixed $value, FieldMetadata $field, ContextInterface $context): mixed;
}
