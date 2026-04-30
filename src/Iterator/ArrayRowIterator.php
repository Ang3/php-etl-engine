<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Iterator;

use Ang3\Component\ETL\Contract\RowInterface;
use Ang3\Component\ETL\Contract\RowIteratorInterface;

readonly class ArrayRowIterator implements RowIteratorInterface
{
    /**
     * @param RowInterface[] $rows
     */
    public function __construct(private array $rows)
    {
    }

    /**
     * @see \IteratorAggregate
     *
     * @return \Traversable<RowInterface>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->rows as $row) {
            yield $row;
        }
    }
}
