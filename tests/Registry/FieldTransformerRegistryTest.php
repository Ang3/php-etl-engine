<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Registry;

use Ang3\Component\ETL\Exception\MissingTransformerException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use Ang3\Component\ETL\Registry\FieldTransformerRegistry;
use Ang3\Component\ETL\Tests\Fixtures\CallableFieldTransformer;
use PHPUnit\Framework\TestCase;

final class FieldTransformerRegistryTest extends TestCase
{
    public function testGetReturnsTheFirstSupportingTransformer(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $unsupported = new CallableFieldTransformer(static fn (mixed $v) => $v, static fn () => false);
        $supported = new CallableFieldTransformer(static fn (mixed $v) => $v, static fn () => true);

        $registry = new FieldTransformerRegistry();
        $registry->add($unsupported);
        $registry->add($supported);

        self::assertSame($supported, $registry->get($field));
    }

    public function testGetThrowsWhenNoTransformerSupportsTheField(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $registry = new FieldTransformerRegistry();
        $registry->add(new CallableFieldTransformer(static fn (mixed $v) => $v, static fn () => false));

        $this->expectException(MissingTransformerException::class);

        $registry->get($field);
    }

    public function testAllReturnsTheTransformersInInsertionOrder(): void
    {
        $first = new CallableFieldTransformer(static fn (mixed $v) => $v);
        $second = new CallableFieldTransformer(static fn (mixed $v) => $v);

        $registry = new FieldTransformerRegistry();
        $registry->add($first);
        $registry->add($second);

        self::assertSame([$first, $second], $registry->all());
    }
}
