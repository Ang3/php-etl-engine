<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Contract;

use Ang3\Component\ETL\Result\ProcessedRow;

/**
 * Writes a processed row.
 *
 * Implementations should use the dataset field metadata from the context
 * and ignore virtual fields, i.e. fields without target metadata.
 */
interface WriterInterface
{
    public function write(ProcessedRow $row, ContextInterface $context): void;
}
