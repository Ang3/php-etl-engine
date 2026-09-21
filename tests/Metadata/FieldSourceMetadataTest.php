<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Metadata;

use Ang3\Component\ETL\Metadata\FieldSourceMetadata;
use PHPUnit\Framework\TestCase;

final class FieldSourceMetadataTest extends TestCase
{
    public function testDefaults(): void
    {
        $source = new FieldSourceMetadata();

        self::assertNull($source->key);
        self::assertSame([], $source->aliases);
        self::assertNull($source->default);
        self::assertFalse($source->required);
    }

    public function testExplicitValues(): void
    {
        $source = new FieldSourceMetadata(key: 'email', aliases: ['mail', 'e-mail'], default: 'n/a', required: true);

        self::assertSame('email', $source->key);
        self::assertSame(['mail', 'e-mail'], $source->aliases);
        self::assertSame('n/a', $source->default);
        self::assertTrue($source->required);
    }
}
