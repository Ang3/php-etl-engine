<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Exception\InvalidFieldValueException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use PHPUnit\Framework\TestCase;

final class InvalidFieldValueExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new InvalidFieldValueException($field, 'nan');

        self::assertSame('Invalid value for field "amount".', $exception->getMessage());
        self::assertSame(EtlErrorCode::InvalidFieldValue, $exception->errorCode());
        self::assertSame($field, $exception->field());
        self::assertSame('nan', $exception->value());
        self::assertSame(['field' => 'amount', 'type' => 'float', 'value' => 'nan'], $exception->errorParameters());
    }

    public function testValueIsStringifiedWhenNotScalar(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new InvalidFieldValueException($field, ['not', 'scalar']);

        self::assertSame('array', $exception->errorParameters()['value']);
    }

    public function testValueIsNullWhenTheValueIsNull(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new InvalidFieldValueException($field, null);

        self::assertNull($exception->errorParameters()['value']);
    }
}
