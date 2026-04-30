<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
        DatasetInterface $dataset,
        RowInterface $input,
        MutableRowInterface $output,
    ): ContextInterface {
        return new DefaultContext($dataset, $input, $output);
    }
}
