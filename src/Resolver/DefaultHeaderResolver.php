<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Resolver;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\HeaderResolverInterface;
use Ang3\Component\ETL\Metadata\FieldSourceMetadata;

final readonly class DefaultHeaderResolver implements HeaderResolverInterface
{
    public function resolve(FieldSourceMetadata $source, ContextInterface $context): ?string
    {
        $candidates = array_values(array_filter([
            $source->key,
            ...$source->aliases,
        ], static fn (?string $value): bool => null !== $value && '' !== $value));

        foreach ($candidates as $candidate) {
            if ($context->hasInput($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
