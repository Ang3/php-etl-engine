<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Iterator;

use Ang3\Component\ETL\Iterator\ArrayRowIterator;
use Ang3\Component\ETL\Metadata\IndexedRowMetadata;
use PHPUnit\Framework\TestCase;

final class ArrayRowIteratorTest extends TestCase
{
    public function testIteratesOverTheGivenRowsInOrder(): void
    {
        $rows = [
            new IndexedRowMetadata(['a' => 1], 0),
            new IndexedRowMetadata(['a' => 2], 1),
        ];
        $iterator = new ArrayRowIterator($rows);

        self::assertSame($rows, iterator_to_array($iterator));
    }

    public function testCanBeIteratedMoreThanOnce(): void
    {
        $rows = [new IndexedRowMetadata(['a' => 1], 0)];
        $iterator = new ArrayRowIterator($rows);

        self::assertCount(1, iterator_to_array($iterator));
        self::assertCount(1, iterator_to_array($iterator));
    }

    public function testAnEmptyArrayYieldsNoRows(): void
    {
        $iterator = new ArrayRowIterator([]);

        self::assertSame([], iterator_to_array($iterator));
    }
}
