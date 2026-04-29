<?php

namespace Ang3\Component\ETL\Contract;

use Ang3\Component\ETL\Metadata\FieldMetadata;

interface DatasetInterface
{
    /**
     * @return array<FieldMetadata>
     */
    public function getFields(): array;

    /**
     * @return iterable<RowInterface>
     */
    public function getRows(): iterable;
}
