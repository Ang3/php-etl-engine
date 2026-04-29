<?php

namespace Ang3\Component\ETL\Iterator;

use Ang3\Component\ETL\Contract\RowIteratorInterface;
use Traversable;

readonly class ArrayRowIterator implements RowIteratorInterface
{
    public function __construct(private array $rows)
    {
    }

    /**
     * @see \IteratorAggregate
     */
    public function getIterator(): Traversable
    {
        foreach ($this->rows as $row) {
            yield $row;
        }
    }
}
