<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final class UnsupportedFieldValueException extends ValidationEtlException
{
    public function __construct(
        private readonly FieldMetadata $field,
        private readonly mixed $value,
        private readonly string $expected,
        ?string $appCode = null,
        array $appCodeParameters = [],
        ?string $message = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: $message ?? sprintf(
                'Unsupported value for field "%s": expected %s, got %s.',
                $field->reference,
                $expected,
                get_debug_type($value),
            ),
            errorCode: EtlErrorCode::UnsupportedFieldValue,
            errorParameters: [
                'field' => $field->reference,
                'type' => $field->type,
                'expected' => $expected,
                'actual' => get_debug_type($value),
            ],
            appCode: $appCode,
            appCodeParameters: $appCodeParameters,
            previous: $previous,
        );
    }

    public function field(): FieldMetadata
    {
        return $this->field;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function expected(): string
    {
        return $this->expected;
    }
}
