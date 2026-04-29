<?php

namespace Ang3\Component\ETL\Factory;

use Ang3\Component\ETL\Context\DefaultContext;
use Ang3\Component\ETL\Contract\ContextFactoryInterface;
use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\MutableRowInterface;
use Ang3\Component\ETL\Contract\RowInterface;

final class DefaultContextFactory implements ContextFactoryInterface
{
    public function create(
        RowInterface $input,
        MutableRowInterface $output,
        DatasetInterface $dataset
    ): ContextInterface {
        return new DefaultContext($input, $output, $dataset);
    }
}