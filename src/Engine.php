<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL;

use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\PipelineInterface;

readonly class Engine
{
    public function __construct(private PipelineInterface $pipeline)
    {
    }

    public function process(DatasetInterface $dataset): void
    {
        $this->pipeline->process($dataset);
    }
}
