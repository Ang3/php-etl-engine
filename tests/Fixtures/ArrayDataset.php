<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Fixtures;

use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\RowInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final readonly class ArrayDataset implements DatasetInterface
{
    /**
     * @param list<FieldMetadata>    $fields
     * @param iterable<RowInterface> $rows
     */
    public function __construct(
        private array $fields,
        private iterable $rows,
    ) {
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getRows(): iterable
    {
        return $this->rows;
    }
}
