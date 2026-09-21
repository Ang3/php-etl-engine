<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Metadata;

use Ang3\Component\ETL\Metadata\FieldTargetMetadata;
use PHPUnit\Framework\TestCase;

final class FieldTargetMetadataTest extends TestCase
{
    public function testExposesSubjectAndField(): void
    {
        $target = new FieldTargetMetadata('Invoice', 'total');

        self::assertSame('Invoice', $target->subject);
        self::assertSame('total', $target->field);
    }
}
