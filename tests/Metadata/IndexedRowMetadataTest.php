<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Metadata;

use Ang3\Component\ETL\Metadata\IndexedRowMetadata;
use PHPUnit\Framework\TestCase;

final class IndexedRowMetadataTest extends TestCase
{
    public function testGetAndHas(): void
    {
        $row = new IndexedRowMetadata(['a' => 1, 'b' => null], 3, 42);

        self::assertSame(1, $row->get('a'));
        self::assertNull($row->get('b'));
        self::assertNull($row->get('missing'));
        self::assertTrue($row->has('a'));
        self::assertTrue($row->has('b'));
        self::assertFalse($row->has('missing'));
    }

    public function testAllReturnsTheRawData(): void
    {
        $data = ['a' => 1, 'b' => 2];
        $row = new IndexedRowMetadata($data, 0);

        self::assertSame($data, $row->all());
    }

    public function testPositionAccessors(): void
    {
        $row = new IndexedRowMetadata([], 5, 12);

        self::assertSame(5, $row->rowIndex());
        self::assertSame(12, $row->sourceLineNumber());
    }

    public function testSourceLineNumberDefaultsToNull(): void
    {
        $row = new IndexedRowMetadata([], 0);

        self::assertNull($row->sourceLineNumber());
    }
}
