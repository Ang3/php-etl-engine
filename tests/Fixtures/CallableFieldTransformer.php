<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Fixtures;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\FieldTransformerInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final class CallableFieldTransformer implements FieldTransformerInterface
{
    /**
     * @param \Closure(mixed, FieldMetadata, ContextInterface): mixed $transform
     * @param (\Closure(FieldMetadata): bool)|null                    $supports
     */
    public function __construct(
        private readonly \Closure $transform,
        private readonly ?\Closure $supports = null,
    ) {
    }

    public function supports(FieldMetadata $field): bool
    {
        return null === $this->supports || ($this->supports)($field);
    }

    public function transform(mixed $value, FieldMetadata $field, ContextInterface $context): mixed
    {
        return ($this->transform)($value, $field, $context);
    }
}
