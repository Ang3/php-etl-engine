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

final class MissingTransformerException extends TechnicalEtlException
{
    public function __construct(
        private readonly FieldMetadata $field,
        ?string $message = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: $message ?? sprintf(
                'No transformer found for field "%s" of type "%s".',
                $field->reference,
                $field->type,
            ),
            errorCode: EtlErrorCode::MissingTransformer,
            errorParameters: [
                'field' => $field->reference,
                'type' => $field->type,
            ],
            previous: $previous,
        );
    }

    public function field(): FieldMetadata
    {
        return $this->field;
    }
}
