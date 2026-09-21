<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Exception\InvalidFieldTransformerException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use PHPUnit\Framework\TestCase;

final class InvalidFieldTransformerExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new InvalidFieldTransformerException($field);

        self::assertSame('Invalid field transformer for field "amount".', $exception->getMessage());
        self::assertSame(EtlErrorCode::InvalidFieldTransformer, $exception->errorCode());
        self::assertSame($field, $exception->field());
        self::assertSame(['field' => 'amount', 'type' => 'float'], $exception->errorParameters());
        self::assertNull($exception->appCode());
        self::assertSame([], $exception->appCodeParameters());
    }

    public function testCustomAppCodeAndMessage(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new InvalidFieldTransformerException($field, appCode: 'APP_1', appCodeParameters: ['x' => 1], message: 'custom');

        self::assertSame('custom', $exception->getMessage());
        self::assertSame('APP_1', $exception->appCode());
        self::assertSame(['x' => 1], $exception->appCodeParameters());
    }
}
