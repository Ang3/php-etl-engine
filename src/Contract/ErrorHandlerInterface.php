<?php

namespace Ang3\Component\ETL\Contract;

use Ang3\Component\ETL\Contract\Enum\ErrorStrategy;
use Ang3\Component\ETL\ErrorHandler\EtlError;

interface ErrorHandlerInterface
{
    public function handle(EtlError $error): ErrorStrategy;
}