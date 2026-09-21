<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Metadata;

use Ang3\Component\ETL\Metadata\FieldMetadata;
use Ang3\Component\ETL\Metadata\FieldTargetMetadata;
use PHPUnit\Framework\TestCase;

final class FieldMetadataTest extends TestCase
{
    public function testFieldWithoutTargetIsVirtual(): void
    {
        $field = new FieldMetadata(reference: 'ref', type: 'string');

        self::assertTrue($field->isVirtual());
        self::assertFalse($field->hasTarget());
    }

    public function testFieldWithTargetIsNotVirtual(): void
    {
        $field = new FieldMetadata(reference: 'ref', type: 'string', target: new FieldTargetMetadata('subject', 'field'));

        self::assertFalse($field->isVirtual());
        self::assertTrue($field->hasTarget());
    }

    public function testGetOptionReturnsDefaultWhenMissing(): void
    {
        $field = new FieldMetadata(reference: 'ref', type: 'string');

        self::assertNull($field->getOption('missing'));
        self::assertSame('fallback', $field->getOption('missing', 'fallback'));
    }

    public function testGetOptionReturnsTheStoredValue(): void
    {
        $field = new FieldMetadata(reference: 'ref', type: 'string', options: ['format' => 'Y-m-d']);

        self::assertSame('Y-m-d', $field->getOption('format'));
    }
}
