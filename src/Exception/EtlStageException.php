<?php

namespace Ang3\Component\ETL\Exception;

use Ang3\Component\ETL\Contract\Enum\ErrorStage;

/**
 * Exception thrown to signal an error specific to a particular stage of an ETL (Extract, Transform, Load) process.
 *
 * This exception is designed to encapsulate additional context about the stage at which
 * the error occurred, aiding in debugging and logging within ETL workflows.
 *
 * @extends \RuntimeException
 */
class EtlStageException extends \RuntimeException
{
    /**
     * @param ErrorStage $stage The stage of the ETL process where the exception was raised.
     * @param string $message An optional descriptive message describing the error.
     * @param int $code An optional error code representing the error type.
     * @param \Throwable|null $previous An optional previous exception used for exception chaining.
     */
    public function __construct(private readonly ErrorStage $stage,
                                string                      $message = '',
                                int                         $code = 0,
                                ?\Throwable                 $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStage(): ErrorStage
    {
        return $this->stage;
    }
}