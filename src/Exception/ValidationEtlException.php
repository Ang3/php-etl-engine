<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;

abstract class ValidationEtlException extends EtlException
{
    /**
     * @param array<string, scalar|null> $errorParameters
     * @param array<string, scalar|null> $appCodeParameters
     */
    public function __construct(
        string $message,
        EtlErrorCode $errorCode,
        array $errorParameters = [],
        ?string $appCode = null,
        array $appCodeParameters = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: $message,
            errorCode: $errorCode,
            errorParameters: $errorParameters,
            appCode: $appCode,
            appCodeParameters: $appCodeParameters,
            previous: $previous,
        );
    }
}
