<?php

namespace Ang3\Component\ETL\Contract;

interface ContextFactoryInterface
{
    public function create(
        RowInterface $input,
        MutableRowInterface $output,
        DatasetInterface $dataset
    ): ContextInterface;
}