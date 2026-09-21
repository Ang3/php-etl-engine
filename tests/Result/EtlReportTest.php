<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Result;

use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\ErrorType;
use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Error\EtlError;
use Ang3\Component\ETL\Result\EtlReport;
use PHPUnit\Framework\TestCase;

final class EtlReportTest extends TestCase
{
    public function testInitialState(): void
    {
        $report = new EtlReport();

        self::assertSame(0, $report->processedRows());
        self::assertSame(0, $report->validRows());
        self::assertSame(0, $report->invalidRows());
        self::assertSame(0, $report->writtenRows());
        self::assertSame(0, $report->errorCount());
        self::assertFalse($report->hasErrors());
        self::assertFalse($report->hasFatalError());
        self::assertNull($report->fatalError());
        self::assertSame([], $report->sampleErrors());
    }

    public function testCounters(): void
    {
        $report = new EtlReport();

        $report->incrementProcessedRows();
        $report->incrementProcessedRows();
        $report->incrementValidRows();
        $report->incrementInvalidRows();
        $report->incrementWrittenRows();

        self::assertSame(2, $report->processedRows());
        self::assertSame(1, $report->validRows());
        self::assertSame(1, $report->invalidRows());
        self::assertSame(1, $report->writtenRows());
    }

    public function testAddErrorIncrementsCountAndSamples(): void
    {
        $report = new EtlReport();
        $error = $this->makeError();

        $report->addError($error);

        self::assertSame(1, $report->errorCount());
        self::assertTrue($report->hasErrors());
        self::assertSame([$error], $report->sampleErrors());
    }

    public function testSampleErrorsAreCappedButTheCountIsNot(): void
    {
        $report = new EtlReport(maxSampleErrors: 2);

        $report->addError($this->makeError());
        $report->addError($this->makeError());
        $report->addError($this->makeError());

        self::assertSame(3, $report->errorCount());
        self::assertCount(2, $report->sampleErrors());
    }

    public function testMarkFatalSetsTheFatalErrorAndCountsAsAnError(): void
    {
        $report = new EtlReport();
        $error = $this->makeError();

        $report->markFatal($error);

        self::assertTrue($report->hasFatalError());
        self::assertSame($error, $report->fatalError());
        self::assertSame(1, $report->errorCount());
        self::assertSame([$error], $report->sampleErrors());
    }

    private function makeError(): EtlError
    {
        return new EtlError(
            errorCode: EtlErrorCode::UnexpectedError,
            type: ErrorType::Technical,
            stage: ErrorStage::Pipeline,
            message: 'boom',
        );
    }
}
