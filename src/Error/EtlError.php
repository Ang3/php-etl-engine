<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Error;

use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\ErrorType;
use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final readonly class EtlError
{
    /**
     * @param array<string, scalar|null> $errorParameters
     * @param array<string, scalar|null> $appCodeParameters
     */
    public function __construct(
        public EtlErrorCode $errorCode,
        public ErrorType $type,
        public ErrorStage $stage,
        public string $message,
        public array $errorParameters = [],
        public ?string $appCode = null,
        public array $appCodeParameters = [],
        public ?FieldMetadata $field = null,
        public mixed $rawValue = null,
        public ?int $rowIndex = null,
        public ?int $sourceLineNumber = null,
        public ?string $exceptionClass = null,
    ) {
    }

    public function fieldReference(): ?string
    {
        return $this->field?->reference;
    }

    public function isValidation(): bool
    {
        return ErrorType::Validation === $this->type;
    }

    public function isTechnical(): bool
    {
        return ErrorType::Technical === $this->type;
    }
}
