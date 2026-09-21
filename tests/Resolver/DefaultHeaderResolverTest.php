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
use Ang3\Component\ETL\Metadata\FieldSourceMetadata;
use Ang3\Component\ETL\Metadata\RowMetadata;
use Ang3\Component\ETL\Resolver\DefaultHeaderResolver;
use PHPUnit\Framework\TestCase;

final class DefaultHeaderResolverTest extends TestCase
{
    private DefaultHeaderResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new DefaultHeaderResolver();
    }

    public function testReturnsTheKeyWhenPresentInTheInput(): void
    {
        $source = new FieldSourceMetadata(key: 'Amount', aliases: ['montant']);
        $context = $this->contextWithInput(['Amount' => '1']);

        self::assertSame('Amount', $this->resolver->resolve($source, $context));
    }

    public function testFallsBackToAnAlias(): void
    {
        $source = new FieldSourceMetadata(key: 'Amount', aliases: ['montant']);
        $context = $this->contextWithInput(['montant' => '1']);

        self::assertSame('montant', $this->resolver->resolve($source, $context));
    }

    public function testReturnsNullWhenNothingMatches(): void
    {
        $source = new FieldSourceMetadata(key: 'Amount', aliases: ['montant']);
        $context = $this->contextWithInput([]);

        self::assertNull($this->resolver->resolve($source, $context));
    }

    public function testIgnoresNullAndEmptyCandidates(): void
    {
        $source = new FieldSourceMetadata(key: null, aliases: ['', 'montant']);
        $context = $this->contextWithInput(['montant' => '1']);

        self::assertSame('montant', $this->resolver->resolve($source, $context));
    }

    /**
     * @param array<string, mixed> $input
     */
    private function contextWithInput(array $input): DefaultContext
    {
        return new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata($input), new RowMetadata());
    }
}
