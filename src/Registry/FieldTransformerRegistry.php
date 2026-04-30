<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Registry;

use Ang3\Component\ETL\Contract\FieldTransformerInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;

class FieldTransformerRegistry
{
    /**
     * @var FieldTransformerInterface[]
     */
    private array $transformers = [];

    public function add(FieldTransformerInterface $transformer): void
    {
        $this->transformers[] = $transformer;
    }

    public function get(FieldMetadata $field): FieldTransformerInterface
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($field)) {
                return $transformer;
            }
        }

        throw new \InvalidArgumentException(sprintf('Missing field "%s".', $field->reference));
    }

    /**
     * @return FieldTransformerInterface[]
     */
    public function all(): array
    {
        return $this->transformers;
    }
}
