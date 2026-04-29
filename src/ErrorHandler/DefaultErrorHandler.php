<?php

namespace Ang3\Component\ETL\ErrorHandler;

use Ang3\Component\ETL\Contract\Enum\ErrorStrategy;
use Ang3\Component\ETL\Contract\ErrorHandlerInterface;

final class DefaultErrorHandler implements ErrorHandlerInterface
{
    public function handle(EtlError $error): ErrorStrategy
    {
        throw $error->exception;
    }
}