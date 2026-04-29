<?php

namespace Ang3\Component\ETL\Contract;

interface ContextInterface
{
    public function input(): RowInterface;

    public function output(): MutableRowInterface;

    public function dataset(): ?DatasetInterface;

    public function getInput(string $key, mixed $default = null): mixed;

    public function hasInput(string $key): bool;

    public function getOutput(string $key, mixed $default = null): mixed;

    public function hasOutput(string $key): bool;

    public function get(string $key, mixed $default = null): mixed;

    public function has(string $key): bool;

    public function set(string $key, mixed $value): void;

    public function getOption(string $key, mixed $default = null): mixed;

    public function hasOption(string $key): bool;

    public function setOption(string $key, mixed $value): void;

    public function getRequired(string $key): mixed;
}