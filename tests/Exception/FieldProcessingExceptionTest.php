<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Exception;

use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Exception\FieldProcessingException;
use Ang3\Component\ETL\Exception\StagedExceptionInterface;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use PHPUnit\Framework\TestCase;

final class FieldProcessingExceptionTest extends TestCase
{
    public function testDefaultMessageAndParameters(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $previous = new \RuntimeException('inner boom');

        $exception = new FieldProcessingException($field, ErrorStage::FieldTransformation, 'raw', $previous);

        self::assertInstanceOf(StagedExceptionInterface::class, $exception);
        self::assertSame('Unexpected error while processing field "amount" at stage "field_transformation".', $exception->getMessage());
        self::assertSame(EtlErrorCode::FieldProcessingFailed, $exception->errorCode());
        self::assertSame($field, $exception->field());
        self::assertSame(ErrorStage::FieldTransformation, $exception->stage());
        self::assertSame('raw', $exception->rawValue());
        self::assertSame($previous, $exception->getPrevious());
        self::assertSame([
            'field' => 'amount',
            'type' => 'float',
            'stage' => 'field_transformation',
            'raw_value' => 'raw',
            'previous_exception' => \RuntimeException::class,
            'previous_message' => 'inner boom',
        ], $exception->errorParameters());
    }

    public function testCustomMessageOverridesTheDefault(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new FieldProcessingException($field, ErrorStage::FieldResolution, null, new \RuntimeException(), 'custom message');

        self::assertSame('custom message', $exception->getMessage());
    }

    public function testRawValueIsStringifiedWhenScalar(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'int');
        $exception = new FieldProcessingException($field, ErrorStage::FieldResolution, 42, new \RuntimeException());

        self::assertSame('42', $exception->errorParameters()['raw_value']);
    }

    public function testRawValueFallsBackToItsTypeWhenNotScalar(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'array');
        $exception = new FieldProcessingException($field, ErrorStage::FieldResolution, new \stdClass(), new \RuntimeException());

        self::assertSame('stdClass', $exception->errorParameters()['raw_value']);
    }

    public function testRawValueIsNullWhenTheRawValueIsNull(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'string');
        $exception = new FieldProcessingException($field, ErrorStage::FieldResolution, null, new \RuntimeException());

        self::assertNull($exception->errorParameters()['raw_value']);
    }
}
