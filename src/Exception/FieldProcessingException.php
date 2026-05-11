<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final class FieldProcessingException extends TechnicalEtlException
{
    public function __construct(
        private readonly FieldMetadata $field,
        private readonly ErrorStage $stage,
        private readonly mixed $rawValue,
        \Throwable $previous,
        ?string $message = null,
    ) {
        parent::__construct(
            message: $message ?? sprintf(
                'Unexpected error while processing field "%s" at stage "%s".',
                $field->reference,
                $stage->value,
            ),
            errorCode: EtlErrorCode::FieldProcessingFailed,
            errorParameters: [
                'field' => $field->reference,
                'type' => $field->type,
                'stage' => $stage->value,
                'raw_value' => self::stringifyValue($rawValue),
                'previous_exception' => $previous::class,
                'previous_message' => $previous->getMessage(),
            ],
            previous: $previous,
        );
    }

    public function field(): FieldMetadata
    {
        return $this->field;
    }

    public function stage(): ErrorStage
    {
        return $this->stage;
    }

    public function rawValue(): mixed
    {
        return $this->rawValue;
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
