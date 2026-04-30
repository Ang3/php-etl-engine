<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Metadata;

use Ang3\Component\ETL\Contract\MutableRowInterface;
use Ang3\Component\ETL\Contract\RowInterface;
use Webmozart\Assert\Assert;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class RowMetadata implements MutableRowInterface, \ArrayAccess
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private array $data = [])
    {
    }

    /**
     * @see RowInterface
     */
    public function get(string $key): mixed
    {
        return $this->offsetGet($key);
    }

    /**
     * @see RowInterface
     */
    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @see RowInterface
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @see \ArrayAccess
     */
    public function offsetExists(mixed $offset): bool
    {
        Assert::string($offset);

        return array_key_exists($offset, $this->data);
    }

    /**
     * @see \ArrayAccess
     */
    public function offsetGet(mixed $offset): mixed
    {
        Assert::string($offset);

        return $this->data[$offset] ?? null;
    }

    /**
     * @see \ArrayAccess
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        Assert::string($offset);
        $this->data[$offset] = $value;
    }

    /**
     * @see \ArrayAccess
     */
    public function offsetUnset(mixed $offset): void
    {
        Assert::string($offset);
        unset($this->data[$offset]);
    }

    public function set(string $key, mixed $value): void
    {
        $this->offsetSet($key, $value);
    }
}
