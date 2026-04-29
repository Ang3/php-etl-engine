<?php

namespace Ang3\Component\ETL\Metadata;

use Ang3\Component\ETL\Contract\MutableRowInterface;
use Ang3\Component\ETL\Contract\RowInterface;
use Webmozart\Assert\Assert;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class RowMetadata implements MutableRowInterface, \ArrayAccess
{
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
