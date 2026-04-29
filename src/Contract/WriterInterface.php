<?php

namespace Ang3\Component\ETL\Contract;

interface WriterInterface
{
    public function write(RowInterface $row, ContextInterface $context): void;
}
