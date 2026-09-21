<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Exception\UnsupportedFieldValueException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use PHPUnit\Framework\TestCase;

final class UnsupportedFieldValueExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new UnsupportedFieldValueException($field, 'not-a-number', 'numeric string');

        self::assertSame(
            'Unsupported value for field "amount": expected numeric string, got string.',
            $exception->getMessage(),
        );
        self::assertSame(EtlErrorCode::UnsupportedFieldValue, $exception->errorCode());
        self::assertSame($field, $exception->field());
        self::assertSame('not-a-number', $exception->value());
        self::assertSame('numeric string', $exception->expected());
        self::assertSame([
            'field' => 'amount',
            'type' => 'float',
            'expected' => 'numeric string',
            'actual' => 'string',
        ], $exception->errorParameters());
    }
}
