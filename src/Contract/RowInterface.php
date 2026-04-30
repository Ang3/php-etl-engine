<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Contract;

interface RowInterface
{
    public function get(string $key): mixed;

    public function has(string $key): bool;

    /**
     * @return array<string, mixed>
     */
    public function all(): array;
}
