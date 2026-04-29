<?php

namespace Ang3\Component\ETL\Contract;

interface PipelineInterface
{
    public function process(DatasetInterface $dataset): void;
}
