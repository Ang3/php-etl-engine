<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Factory;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\ErrorType;
use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Contract\ErrorFactoryInterface;
use Ang3\Component\ETL\Error\EtlError;
use Ang3\Component\ETL\Exception\EtlException;
use Ang3\Component\ETL\Exception\StagedExceptionInterface;
use Ang3\Component\ETL\Exception\TechnicalEtlException;
use Ang3\Component\ETL\Exception\ValidationEtlException;
use Ang3\Component\ETL\Metadata\FieldMetadata;

final readonly class DefaultErrorFactory implements ErrorFactoryInterface
{
    public function create(
        \Throwable $exception,
        ErrorStage $stage,
        ContextInterface $context,
        ?FieldMetadata $field = null,
        mixed $rawValue = null,
    ): EtlError {
        $etlException = $this->findEtlException($exception);

        return new EtlError(
            errorCode: $etlException?->errorCode() ?? EtlErrorCode::UnexpectedError,
            type: $this->resolveType($exception),
            stage: $this->resolveStage($exception, $stage),
            message: $exception->getMessage(),
            errorParameters: $etlException?->errorParameters() ?? [
                'exception' => $exception::class,
            ],
            appCode: $etlException?->appCode(),
            appCodeParameters: $etlException?->appCodeParameters() ?? [],
            field: $field,
            rawValue: $rawValue,
            rowIndex: $context->rowIndex(),
            sourceLineNumber: $context->sourceLineNumber(),
            exceptionClass: $exception::class,
        );
    }

    private function resolveType(\Throwable $exception): ErrorType
    {
        $current = $exception;

        while (null !== $current) {
            if ($current instanceof ValidationEtlException) {
                return ErrorType::Validation;
            }

            if ($current instanceof TechnicalEtlException) {
                return ErrorType::Technical;
            }

            $current = $current->getPrevious();
        }

        return ErrorType::Technical;
    }

    /**
     * @internal
     */
    private function resolveStage(\Throwable $exception, ErrorStage $fallback): ErrorStage
    {
        $current = $exception;

        while (null !== $current) {
            if ($current instanceof StagedExceptionInterface) {
                return $current->stage();
            }

            $current = $current->getPrevious();
        }

        return $fallback;
    }

    /**
     * @internal
     */
    private function findEtlException(\Throwable $exception): ?EtlException
    {
        $current = $exception;

        while (null !== $current) {
            if ($current instanceof EtlException) {
                return $current;
            }

            $current = $current->getPrevious();
        }

        return null;
    }
}
