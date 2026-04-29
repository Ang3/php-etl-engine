<?php

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