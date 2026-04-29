<?php

namespace Ang3\Component\ETL\Registry;

use Ang3\Component\ETL\Contract\FieldTransformerInterface;
use Ang3\Component\ETL\Exception\MissingTransformerException;
use Ang3\Component\ETL\Metadata\FieldMetadata;

class FieldTransformerRegistry
{
    /**
     * @var FieldTransformerInterface[]
     */
    private array $transformers = [];

    public function add(object $service): void
    {
        $this->transformers[] = $service;
    }

    /**
     * @throws MissingTransformerException when the transformer was not found
     */
    public function get(FieldMetadata $field): object
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($field)) {
                return $transformer;
            }
        }

        throw new MissingTransformerException($field);
    }

    /**
     * @return FieldTransformerInterface[]
     */
    public function all(): array
    {
        return $this->transformers;
    }
}
