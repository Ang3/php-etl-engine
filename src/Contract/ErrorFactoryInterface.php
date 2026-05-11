<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Contract;

use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Error\EtlError;
use Ang3\Component\ETL\Metadata\FieldMetadata;

interface ErrorFactoryInterface
{
    public function create(\Throwable $exception,
        ErrorStage $stage,
        ContextInterface $context,
        ?FieldMetadata $field = null,
        mixed $rawValue = null): EtlError;
}
