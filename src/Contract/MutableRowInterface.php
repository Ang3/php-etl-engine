<?php

namespace Ang3\Component\ETL\Contract;

interface MutableRowInterface extends RowInterface
{
    public function set(string $key, mixed $value): void;
}