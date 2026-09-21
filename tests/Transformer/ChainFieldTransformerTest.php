<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Transformer;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\FieldTransformerInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use Ang3\Component\ETL\Transformer\ChainFieldTransformer;
use PHPUnit\Framework\TestCase;

final class ChainFieldTransformerTest extends TestCase
{
    public function testTransformAppliesEachTransformerInOrder(): void
    {
        $field = new FieldMetadata(reference: 'field', type: 'string');
        $context = $this->createStub(ContextInterface::class);

        $chain = new ChainFieldTransformer(
            $this->transformerAppending('a'),
            $this->transformerAppending('b'),
            $this->transformerAppending('c'),
        );

        self::assertSame('a-b-c', $chain->transform('', $field, $context));
    }

    public function testSupportsRequiresEveryTransformerToSupportTheField(): void
    {
        $field = new FieldMetadata(reference: 'field', type: 'string');

        $chain = new ChainFieldTransformer(
            $this->transformerSupporting(true),
            $this->transformerSupporting(false),
        );

        self::assertFalse($chain->supports($field));
    }

    public function testSupportsIsTrueWhenAllTransformersSupportTheField(): void
    {
        $field = new FieldMetadata(reference: 'field', type: 'string');

        $chain = new ChainFieldTransformer(
            $this->transformerSupporting(true),
            $this->transformerSupporting(true),
        );

        self::assertTrue($chain->supports($field));
    }

    public function testConstructorRejectsAnEmptyChain(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ChainFieldTransformer();
    }

    private function transformerAppending(string $suffix): FieldTransformerInterface
    {
        return new class($suffix) implements FieldTransformerInterface {
            public function __construct(private string $suffix)
            {
            }

            public function supports(FieldMetadata $field): bool
            {
                return true;
            }

            public function transform(mixed $value, FieldMetadata $field, ContextInterface $context): mixed
            {
                return is_string($value) && '' !== $value ? $value.'-'.$this->suffix : $this->suffix;
            }
        };
    }

    private function transformerSupporting(bool $supports): FieldTransformerInterface
    {
        return new class($supports) implements FieldTransformerInterface {
            public function __construct(private bool $supports)
            {
            }

            public function supports(FieldMetadata $field): bool
            {
                return $this->supports;
            }

            public function transform(mixed $value, FieldMetadata $field, ContextInterface $context): mixed
            {
                return $value;
            }
        };
    }
}
