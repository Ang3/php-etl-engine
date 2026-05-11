<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Resolver;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\FieldValueResolverInterface;
use Ang3\Component\ETL\Contract\HeaderResolverInterface;
use Ang3\Component\ETL\Exception\MissingRequiredFieldValueException;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final readonly class DefaultFieldValueResolver implements FieldValueResolverInterface
{
    public function __construct(
        private HeaderResolverInterface $headerResolver,
    ) {
    }

    public function resolve(FieldMetadata $field, ContextInterface $context): mixed
    {
        $source = $field->source;

        if (null === $source) {
            return $context->getInput($field->reference);
        }

        $key = $this->headerResolver->resolve($source, $context);

        if (null !== $key) {
            return $context->getInput($key);
        }

        if ($source->required) {
            throw new MissingRequiredFieldValueException(
                field: $field,
                sourceKey: $source->key,
            );
        }

        return $source->default;
    }
}
