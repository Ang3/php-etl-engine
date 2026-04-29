<?php

namespace Ang3\Component\ETL\Contract;

interface OutputRowFactoryInterface
{
    public function create(): MutableRowInterface;
}