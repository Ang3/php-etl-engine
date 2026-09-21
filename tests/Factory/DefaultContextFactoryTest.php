<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Factory;

use Ang3\Component\ETL\Context\DefaultContext;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Factory\DefaultContextFactory;
use Ang3\Component\ETL\Metadata\RowMetadata;
use PHPUnit\Framework\TestCase;

final class DefaultContextFactoryTest extends TestCase
{
    public function testCreateReturnsADefaultContextWiredWithTheGivenCollaborators(): void
    {
        $dataset = $this->createStub(DatasetInterface::class);
        $input = new RowMetadata(['a' => 1]);
        $output = new RowMetadata();

        $context = (new DefaultContextFactory())->create($dataset, $input, $output);

        self::assertInstanceOf(DefaultContext::class, $context);
        self::assertSame($dataset, $context->dataset());
        self::assertSame($input, $context->input());
        self::assertSame($output, $context->output());
    }
}
