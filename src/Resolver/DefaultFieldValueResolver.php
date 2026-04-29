<?php

namespace Ang3\Component\ETL\Resolver;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\FieldValueResolverInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final readonly class DefaultFieldValueResolver implements FieldValueResolverInterface
{
    public function resolve(FieldMetadata $field, ContextInterface $context): mixed
    {
        if ($field->sourceKey !== null) {
            return $context->getInput($field->sourceKey);
        }

        return $context->getInput($field->reference);
    }
}