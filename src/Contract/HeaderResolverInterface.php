<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Contract;

use Ang3\Component\ETL\Metadata\FieldSourceMetadata;

interface HeaderResolverInterface
{
    public function resolve(FieldSourceMetadata $source, ContextInterface $context): ?string;
}
