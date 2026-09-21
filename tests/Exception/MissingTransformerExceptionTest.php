<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Exception;

use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Exception\MissingTransformerException;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use PHPUnit\Framework\TestCase;

final class MissingTransformerExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new MissingTransformerException($field);

        self::assertSame('No transformer found for field "amount" of type "float".', $exception->getMessage());
        self::assertSame(EtlErrorCode::MissingTransformer, $exception->errorCode());
        self::assertSame($field, $exception->field());
        self::assertSame(['field' => 'amount', 'type' => 'float'], $exception->errorParameters());
    }
}
