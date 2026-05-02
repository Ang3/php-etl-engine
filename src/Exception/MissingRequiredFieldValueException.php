<?php

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final class RequiredFieldValueMissingException extends ValidationEtlException
{
    public function __construct(
        private readonly FieldMetadata $field,
        private readonly ?string $sourceKey = null,
        ?string $appCode = null,
        array $appCodeParameters = [],
        ?string $message = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: $message ?? sprintf(
            'Missing required value for field "%s".',
            $field->reference,
        ),
            errorCode: EtlErrorCode::MissingRequiredField,
            errorParameters: [
                'field' => $field->reference,
                'type' => $field->type,
                'source' => $sourceKey,
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

    public function sourceKey(): ?string
    {
        return $this->sourceKey;
    }
}