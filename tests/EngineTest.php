<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests;

use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\PipelineInterface;
use Ang3\Component\ETL\Engine;
use Ang3\Component\ETL\Result\EtlReport;
use PHPUnit\Framework\TestCase;

final class EngineTest extends TestCase
{
    public function testProcessDelegatesToThePipelineAndReturnsItsReport(): void
    {
        $dataset = $this->createStub(DatasetInterface::class);
        $report = new EtlReport();
        $pipeline = $this->createMock(PipelineInterface::class);
        $pipeline->expects(self::once())
            ->method('process')
            ->with($dataset)
            ->willReturn($report);

        $engine = new Engine($pipeline);

        self::assertSame($report, $engine->process($dataset));
    }
}
