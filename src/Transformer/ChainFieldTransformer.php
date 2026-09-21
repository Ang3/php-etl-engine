<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Transformer;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\FieldTransformerInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use Webmozart\Assert\Assert;

final readonly class ChainFieldTransformer implements FieldTransformerInterface
{
    /**
     * @var list<FieldTransformerInterface>
     */
    private array $transformers;

    public function __construct(FieldTransformerInterface ...$transformers)
    {
        Assert::notEmpty($transformers, 'A chain field transformer requires at least one field transformer.');

        $this->transformers = array_values($transformers);
    }

    /**
     * Supports the field only if every chained transformer supports it.
     */
    public function supports(FieldMetadata $field): bool
    {
        foreach ($this->transformers as $transformer) {
            if (!$transformer->supports($field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Applies each chained transformer in order, passing the output of one as the input of the next.
     */
    public function transform(mixed $value, FieldMetadata $field, ContextInterface $context): mixed
    {
        foreach ($this->transformers as $transformer) {
            $value = $transformer->transform($value, $field, $context);
        }

        return $value;
    }

    /**
     * @return list<FieldTransformerInterface>
     */
    public function transformers(): array
    {
        return $this->transformers;
    }
}
