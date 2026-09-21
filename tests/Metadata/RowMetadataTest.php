<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Metadata;

use Ang3\Component\ETL\Metadata\RowMetadata;
use PHPUnit\Framework\TestCase;

final class RowMetadataTest extends TestCase
{
    public function testGetHasAllOnAnEmptyRow(): void
    {
        $row = new RowMetadata();

        self::assertNull($row->get('a'));
        self::assertFalse($row->has('a'));
        self::assertSame([], $row->all());
    }

    public function testConstructorAcceptsInitialData(): void
    {
        $row = new RowMetadata(['a' => 1]);

        self::assertTrue($row->has('a'));
        self::assertSame(1, $row->get('a'));
    }

    public function testSetWritesAValue(): void
    {
        $row = new RowMetadata();
        $row->set('a', 'value');

        self::assertTrue($row->has('a'));
        self::assertSame('value', $row->get('a'));
        self::assertSame(['a' => 'value'], $row->all());
    }

    public function testArrayAccess(): void
    {
        $row = new RowMetadata();

        self::assertFalse(isset($row['a']));

        $row['a'] = 'value';

        self::assertTrue(isset($row['a']));
        self::assertSame('value', $row['a']);

        unset($row['a']);

        self::assertFalse(isset($row['a']));
        self::assertNull($row['a']);
    }

    public function testArrayAccessRejectsANonStringOffset(): void
    {
        $row = new RowMetadata();

        $this->expectException(\InvalidArgumentException::class);

        // Deliberately violating the declared @implements \ArrayAccess<string, mixed> to exercise the runtime guard.
        // @phpstan-ignore-next-line argument.type
        $row->offsetGet(0);
    }
}
