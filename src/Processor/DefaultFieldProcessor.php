<?php

namespace Ang3\Component\ETL\Processor;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\FieldProcessorInterface;
use Ang3\Component\ETL\Contract\FieldValueResolverInterface;
use Ang3\Component\ETL\Exception\EtlException;
use Ang3\Component\ETL\Exception\FieldProcessingException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use Ang3\Component\ETL\Registry\FieldTransformerRegistry;

final readonly class DefaultFieldProcessor implements FieldProcessorInterface
{
    public function __construct(
        private FieldValueResolverInterface $valueResolver,
        private FieldTransformerRegistry $transformers,
    ) {
    }

    public function process(FieldMetadata $field, ContextInterface $context): void
    {
        $rawValue = null;

        try {
            $rawValue = $this->valueResolver->resolve($field, $context);
            $transformer = $this->transformers->get($field);
            $value = $transformer->transform($rawValue, $field, $context);
            $context->set($field->reference, $value);
        } catch (EtlException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new FieldProcessingException(
                field: $field,
                rawValue: $rawValue,
                previous: $e,
            );
        }
    }
}