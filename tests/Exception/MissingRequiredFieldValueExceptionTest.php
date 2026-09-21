<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Exception\MissingRequiredFieldValueException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use PHPUnit\Framework\TestCase;

final class MissingRequiredFieldValueExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new MissingRequiredFieldValueException($field, 'Amount');

        self::assertSame('Missing required value for field "amount".', $exception->getMessage());
        self::assertSame(EtlErrorCode::MissingRequiredField, $exception->errorCode());
        self::assertSame($field, $exception->field());
        self::assertSame('Amount', $exception->sourceKey());
        self::assertSame(['field' => 'amount', 'type' => 'float', 'source' => 'Amount'], $exception->errorParameters());
    }

    public function testSourceKeyDefaultsToNull(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new MissingRequiredFieldValueException($field);

        self::assertNull($exception->sourceKey());
        self::assertNull($exception->errorParameters()['source']);
    }
}
