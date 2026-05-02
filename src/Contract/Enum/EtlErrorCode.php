<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Contract\Enum;

enum EtlErrorCode: string
{
    case MissingRequiredField = 'missing_required_field';
    case InvalidFieldValue = 'invalid_field_value';
    case UnsupportedFieldValue = 'unsupported_field_value';

    case MissingTransformer = 'missing_transformer';
    case InvalidFieldTransformer = 'invalid_field_transformer';
    case FieldProcessingFailed = 'field_processing_failed';
    case WriterFailed = 'writer_failed';
    case UnexpectedError = 'unexpected_error';
}
