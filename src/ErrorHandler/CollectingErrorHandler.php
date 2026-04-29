<?php

namespace Ang3\Component\ETL\ErrorHandler;

use Ang3\Component\ETL\Contract\Enum\ErrorStrategy;
use Ang3\Component\ETL\Contract\ErrorHandlerInterface;

final class CollectingErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var EtlError[]
     */
    private array $errors = [];

    public function handle(EtlError $error): ErrorStrategy
    {
        $this->errors[] = $error;

        return ErrorStrategy::Continue;
    }

    /**
     * @return EtlError[]
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }
}