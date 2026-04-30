<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Metadata;

final readonly class FieldTargetMetadata
{
    public function __construct(
        public string $subject,
        public string $field,
    ) {
    }
}
