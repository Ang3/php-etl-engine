<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Result;

use Ang3\Component\ETL\Contract\Enum\RowStatus;
use Ang3\Component\ETL\Contract\RowInterface;

final readonly class ProcessedRow
{
    /**
     * @param array<int, mixed> $errors
     */
    public function __construct(
        public RowInterface $input,
        public RowInterface $output,
        public RowStatus $status,
        public ?int $rowIndex = null,
        public ?int $sourceLineNumber = null,
        public array $errors = [],
    ) {
    }

    public function isValid(): bool
    {
        return RowStatus::Valid === $this->status;
    }

    public function isInvalid(): bool
    {
        return RowStatus::Invalid === $this->status;
    }
}
