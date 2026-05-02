<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;

abstract class EtlException extends \RuntimeException
{
    /**
     * @param array<string, scalar|null> $errorParameters
     * @param array<string, scalar|null> $appCodeParameters
     */
    public function __construct(
        string $message,
        private readonly EtlErrorCode $errorCode,
        private readonly array $errorParameters = [],
        private readonly ?string $appCode = null,
        private readonly array $appCodeParameters = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function errorCode(): EtlErrorCode
    {
        return $this->errorCode;
    }

    /**
     * Parameters related to the ETL engine error code.
     *
     * @return array<string, scalar|null>
     */
    public function errorParameters(): array
    {
        return $this->errorParameters;
    }

    public function appCode(): ?string
    {
        return $this->appCode;
    }

    /**
     * Parameters related to the application-specific error code.
     *
     * @return array<string, scalar|null>
     */
    public function appCodeParameters(): array
    {
        return $this->appCodeParameters;
    }
}
