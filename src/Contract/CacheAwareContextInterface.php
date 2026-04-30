<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Contract;

interface CacheAwareContextInterface extends ContextInterface
{
    public function remember(string $key, mixed $value): void;

    public function recall(string $key, mixed $default = null): mixed;

    public function hasCached(string $key): bool;
}
