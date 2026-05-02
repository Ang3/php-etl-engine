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

final class InvalidFieldValueException extends ValidationEtlException
{
    public function __construct(
        private readonly FieldMetadata $field,
        private readonly mixed $value,
        ?string $appCode = null,
        array $appCodeParameters = [],
        ?string $message = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: $message ?? sprintf(
                'Invalid value for field "%s".',
                $field->reference,
            ),
            errorCode: EtlErrorCode::InvalidFieldValue,
            errorParameters: [
                'field' => $field->reference,
                'type' => $field->type,
                'value' => self::stringifyValue($value),
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

    private static function stringifyValue(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return get_debug_type($value);
    }
}
