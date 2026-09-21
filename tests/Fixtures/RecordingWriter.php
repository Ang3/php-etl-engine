<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Fixtures;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\WriterInterface;
use Ang3\Component\ETL\Result\ProcessedRow;

final class RecordingWriter implements WriterInterface
{
    /**
     * @var list<ProcessedRow>
     */
    public array $written = [];

    /**
     * @var list<ContextInterface>
     */
    public array $contexts = [];

    public function __construct(private readonly bool $failing = false)
    {
    }

    public function write(ProcessedRow $row, ContextInterface $context): void
    {
        if ($this->failing) {
            throw new \RuntimeException('The writer is down.');
        }

        $this->written[] = $row;
        $this->contexts[] = $context;
    }
}
