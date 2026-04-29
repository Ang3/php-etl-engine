<?php

namespace Ang3\Component\ETL\Contract;

interface RowInterface
{
    public function get(string $key): mixed;

    public function has(string $key): bool;

    public function all(): array;
}
