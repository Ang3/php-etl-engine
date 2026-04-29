<?php

namespace Ang3\Component\ETL\Contract;

interface CacheAwareContextInterface extends ContextInterface
{
    public function remember(string $key, mixed $value): void;

    public function recall(string $key, mixed $default = null): mixed;

    public function hasCached(string $key): bool;
}