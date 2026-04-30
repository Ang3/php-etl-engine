<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Metadata;

class FieldMetadata
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public string $reference,
        public string $type,
        public ?FieldTargetMetadata $target = null,
        public ?FieldSourceMetadata $source = null,
        public array $options = [],
    ) {
    }

    public function isVirtual(): bool
    {
        return null === $this->target;
    }

    public function hasTarget(): bool
    {
        return null !== $this->target;
    }

    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }
}
