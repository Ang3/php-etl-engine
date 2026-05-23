<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Result;

use Ang3\Component\ETL\Error\EtlError;

final class EtlReport
{
    /**
     * @var EtlError[]
     */
    private array $sampleErrors = [];

    private int $processedRows = 0;
    private int $validRows = 0;
    private int $invalidRows = 0;
    private int $writtenRows = 0;
    private int $errorCount = 0;

    public function __construct(
        private readonly int $maxSampleErrors = 100,
        private ?EtlError $fatalError = null,
    ) {
    }

    public function incrementProcessedRows(): void
    {
        ++$this->processedRows;
    }

    public function incrementValidRows(): void
    {
        ++$this->validRows;
    }

    public function incrementInvalidRows(): void
    {
        ++$this->invalidRows;
    }

    public function incrementWrittenRows(): void
    {
        ++$this->writtenRows;
    }

    public function addError(EtlError $error): void
    {
        ++$this->errorCount;

        if (count($this->sampleErrors) < $this->maxSampleErrors) {
            $this->sampleErrors[] = $error;
        }
    }

    public function markFatal(EtlError $error): void
    {
        $this->fatalError = $error;
        $this->addError($error);
    }

    public function processedRows(): int
    {
        return $this->processedRows;
    }

    public function validRows(): int
    {
        return $this->validRows;
    }

    public function invalidRows(): int
    {
        return $this->invalidRows;
    }

    public function writtenRows(): int
    {
        return $this->writtenRows;
    }

    public function errorCount(): int
    {
        return $this->errorCount;
    }

    public function hasErrors(): bool
    {
        return $this->errorCount > 0;
    }

    public function hasFatalError(): bool
    {
        return null !== $this->fatalError;
    }

    public function fatalError(): ?EtlError
    {
        return $this->fatalError;
    }

    /**
     * Returns only a limited sample of errors.
     *
     * Full error persistence should be handled by a writer or error sink.
     *
     * @return EtlError[]
     */
    public function sampleErrors(): array
    {
        return $this->sampleErrors;
    }
}
