<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Processor;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\Enum\ErrorStage;
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
        $rawValue = $this->resolveValue($field, $context);
        $value = $this->transformValue($field, $context, $rawValue);
        $context->set($field->reference, $value);
    }

    /**
     * @internal
     */
    private function resolveValue(FieldMetadata $field, ContextInterface $context): mixed
    {
        try {
            return $this->valueResolver->resolve($field, $context);
        } catch (EtlException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new FieldProcessingException(field: $field, stage: ErrorStage::FieldResolution, rawValue: null, previous: $e);
        }
    }

    /**
     * @internal
     */
    private function transformValue(FieldMetadata $field, ContextInterface $context, mixed $rawValue): mixed
    {
        try {
            $transformer = $this->transformers->get($field);

            return $transformer->transform($rawValue, $field, $context);
        } catch (EtlException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new FieldProcessingException(field: $field, stage: ErrorStage::FieldTransformation, rawValue: $rawValue, previous: $e);
        }
    }
}
