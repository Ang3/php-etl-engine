<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
