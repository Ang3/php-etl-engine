<?php

namespace Ang3\Component\ETL\Factory;

use Ang3\Component\ETL\Contract\MutableRowInterface;
use Ang3\Component\ETL\Contract\OutputRowFactoryInterface;
use Ang3\Component\ETL\Metadata\RowMetadata;

final class DefaultOutputRowFactory implements OutputRowFactoryInterface
{
    public function create(): MutableRowInterface
    {
        return new RowMetadata();
    }
}