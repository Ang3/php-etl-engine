<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Metadata;

final readonly class FieldSourceMetadata
{
    /**
     * @param string[] $aliases
     */
    public function __construct(
        public ?string $key = null,
        public array $aliases = [],
        public mixed $default = null,
        public bool $required = false,
    ) {
    }
}
