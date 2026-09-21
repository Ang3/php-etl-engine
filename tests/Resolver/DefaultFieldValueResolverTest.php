<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Resolver;

use Ang3\Component\ETL\Context\DefaultContext;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Exception\MissingRequiredFieldValueException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use Ang3\Component\ETL\Metadata\FieldSourceMetadata;
use Ang3\Component\ETL\Metadata\RowMetadata;
use Ang3\Component\ETL\Resolver\DefaultFieldValueResolver;
use Ang3\Component\ETL\Resolver\DefaultHeaderResolver;
use PHPUnit\Framework\TestCase;

final class DefaultFieldValueResolverTest extends TestCase
{
    private DefaultFieldValueResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new DefaultFieldValueResolver(new DefaultHeaderResolver());
    }

    public function testResolvesByReferenceWhenTheFieldHasNoSource(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $context = $this->contextWithInput(['amount' => '12.5']);

        self::assertSame('12.5', $this->resolver->resolve($field, $context));
    }

    public function testResolvesThroughTheHeaderResolverWhenTheFieldHasASource(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float', source: new FieldSourceMetadata(key: 'Amount', aliases: ['montant']));
        $context = $this->contextWithInput(['montant' => '12.5']);

        self::assertSame('12.5', $this->resolver->resolve($field, $context));
    }

    public function testThrowsWhenARequiredSourceIsMissing(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float', source: new FieldSourceMetadata(key: 'Amount', required: true));
        $context = $this->contextWithInput([]);

        $this->expectException(MissingRequiredFieldValueException::class);

        $this->resolver->resolve($field, $context);
    }

    public function testFallsBackToTheSourceDefaultWhenOptionalAndMissing(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float', source: new FieldSourceMetadata(key: 'Amount', default: '0'));
        $context = $this->contextWithInput([]);

        self::assertSame('0', $this->resolver->resolve($field, $context));
    }

    /**
     * @param array<string, mixed> $input
     */
    private function contextWithInput(array $input): DefaultContext
    {
        return new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata($input), new RowMetadata());
    }
}
