<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Metadata;

use Ang3\Component\ETL\Contract\PositionedRowInterface;

final readonly class IndexedRowMetadata implements PositionedRowInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private array $data,
        private int $rowIndex,
        private ?int $sourceLineNumber = null,
    ) {
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function all(): array
    {
        return $this->data;
    }

    public function rowIndex(): int
    {
        return $this->rowIndex;
    }

    public function sourceLineNumber(): ?int
    {
        return $this->sourceLineNumber;
    }
}
