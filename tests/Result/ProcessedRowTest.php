<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Result;

use Ang3\Component\ETL\Contract\Enum\RowStatus;
use Ang3\Component\ETL\Metadata\IndexedRowMetadata;
use Ang3\Component\ETL\Metadata\RowMetadata;
use Ang3\Component\ETL\Result\ProcessedRow;
use PHPUnit\Framework\TestCase;

final class ProcessedRowTest extends TestCase
{
    public function testValidRow(): void
    {
        $row = new ProcessedRow(
            input: new IndexedRowMetadata([], 0),
            output: new RowMetadata(),
            status: RowStatus::Valid,
        );

        self::assertTrue($row->isValid());
        self::assertFalse($row->isInvalid());
    }

    public function testInvalidRow(): void
    {
        $row = new ProcessedRow(
            input: new IndexedRowMetadata([], 0),
            output: new RowMetadata(),
            status: RowStatus::Invalid,
        );

        self::assertFalse($row->isValid());
        self::assertTrue($row->isInvalid());
    }
}
