<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Factory;

use Ang3\Component\ETL\Contract\MutableRowInterface;
use Ang3\Component\ETL\Factory\DefaultOutputRowFactory;
use Ang3\Component\ETL\Metadata\RowMetadata;
use PHPUnit\Framework\TestCase;

final class DefaultOutputRowFactoryTest extends TestCase
{
    public function testCreateReturnsAnEmptyMutableRow(): void
    {
        $row = (new DefaultOutputRowFactory())->create();

        self::assertInstanceOf(MutableRowInterface::class, $row);
        self::assertInstanceOf(RowMetadata::class, $row);
        self::assertSame([], $row->all());
    }

    public function testEachCallReturnsAFreshInstance(): void
    {
        $factory = new DefaultOutputRowFactory();

        $first = $factory->create();
        $first->set('a', 1);

        $second = $factory->create();

        self::assertFalse($second->has('a'));
    }
}
