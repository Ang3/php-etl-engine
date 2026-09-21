<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Context;

use Ang3\Component\ETL\Context\DefaultContext;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Metadata\IndexedRowMetadata;
use Ang3\Component\ETL\Metadata\RowMetadata;
use PHPUnit\Framework\TestCase;

final class DefaultContextTest extends TestCase
{
    public function testAccessorsExposeTheInjectedCollaborators(): void
    {
        $dataset = $this->createStub(DatasetInterface::class);
        $input = new RowMetadata(['a' => 1]);
        $output = new RowMetadata();
        $context = new DefaultContext($dataset, $input, $output);

        self::assertSame($dataset, $context->dataset());
        self::assertSame($input, $context->input());
        self::assertSame($output, $context->output());
    }

    public function testRowIndexAndSourceLineNumberAreNullForNonPositionedInput(): void
    {
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(), new RowMetadata());

        self::assertNull($context->rowIndex());
        self::assertNull($context->sourceLineNumber());
    }

    public function testRowIndexAndSourceLineNumberDelegateToPositionedInput(): void
    {
        $input = new IndexedRowMetadata([], 3, 42);
        $context = new DefaultContext($this->createStub(DatasetInterface::class), $input, new RowMetadata());

        self::assertSame(3, $context->rowIndex());
        self::assertSame(42, $context->sourceLineNumber());
    }

    public function testGetInputAndHasInput(): void
    {
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(['a' => 1]), new RowMetadata());

        self::assertTrue($context->hasInput('a'));
        self::assertSame(1, $context->getInput('a'));
        self::assertFalse($context->hasInput('missing'));
        self::assertSame('default', $context->getInput('missing', 'default'));
    }

    public function testGetOutputAndHasOutput(): void
    {
        $output = new RowMetadata();
        $output->set('a', 1);
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(), $output);

        self::assertTrue($context->hasOutput('a'));
        self::assertSame(1, $context->getOutput('a'));
        self::assertFalse($context->hasOutput('missing'));
    }

    public function testSetWritesToTheOutputRow(): void
    {
        $output = new RowMetadata();
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(), $output);

        $context->set('a', 'value');

        self::assertSame('value', $output->get('a'));
    }

    public function testGetResolutionOrderPrefersCacheOverOutputOverInput(): void
    {
        $input = new RowMetadata(['key' => 'from-input']);
        $output = new RowMetadata(['key' => 'from-output']);
        $context = new DefaultContext($this->createStub(DatasetInterface::class), $input, $output);

        self::assertSame('from-output', $context->get('key'));

        $context->remember('key', 'from-cache');

        self::assertSame('from-cache', $context->get('key'));
    }

    public function testGetFallsBackToInputThenTheDefault(): void
    {
        $input = new RowMetadata(['key' => 'from-input']);
        $context = new DefaultContext($this->createStub(DatasetInterface::class), $input, new RowMetadata());

        self::assertSame('from-input', $context->get('key'));
        self::assertSame('fallback', $context->get('missing', 'fallback'));
    }

    public function testHasReflectsCacheOutputAndInput(): void
    {
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(), new RowMetadata());

        self::assertFalse($context->has('key'));

        $context->remember('key', null);

        self::assertTrue($context->has('key'));
    }

    public function testRememberRecallAndHasCached(): void
    {
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(), new RowMetadata());

        self::assertFalse($context->hasCached('key'));
        self::assertNull($context->recall('key'));
        self::assertSame('fallback', $context->recall('key', 'fallback'));

        $context->remember('key', 'value');

        self::assertTrue($context->hasCached('key'));
        self::assertSame('value', $context->recall('key'));
    }

    public function testOptions(): void
    {
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(), new RowMetadata());

        self::assertFalse($context->hasOption('opt'));
        self::assertNull($context->getOption('opt'));
        self::assertSame('fallback', $context->getOption('opt', 'fallback'));

        $context->setOption('opt', true);

        self::assertTrue($context->hasOption('opt'));
        self::assertTrue($context->getOption('opt'));
    }

    public function testGetRequiredReturnsTheValueWhenPresent(): void
    {
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(['key' => 'value']), new RowMetadata());

        self::assertSame('value', $context->getRequired('key'));
    }

    public function testGetRequiredThrowsWhenMissing(): void
    {
        $context = new DefaultContext($this->createStub(DatasetInterface::class), new RowMetadata(), new RowMetadata());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing required key "key"');

        $context->getRequired('key');
    }
}
