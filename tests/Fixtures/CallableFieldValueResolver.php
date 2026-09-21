<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Fixtures;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\FieldValueResolverInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final class CallableFieldValueResolver implements FieldValueResolverInterface
{
    /**
     * @param \Closure(FieldMetadata, ContextInterface): mixed $resolve
     */
    public function __construct(private readonly \Closure $resolve)
    {
    }

    public function resolve(FieldMetadata $field, ContextInterface $context): mixed
    {
        return ($this->resolve)($field, $context);
    }
}
