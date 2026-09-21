<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Error;

use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\ErrorType;
use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Error\EtlError;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use PHPUnit\Framework\TestCase;

final class EtlErrorTest extends TestCase
{
    public function testFieldReferenceIsNullWithoutAField(): void
    {
        $error = $this->makeError(ErrorType::Technical);

        self::assertNull($error->fieldReference());
    }

    public function testFieldReferenceReturnsTheFieldReference(): void
    {
        $error = $this->makeError(ErrorType::Technical, field: new FieldMetadata(reference: 'amount', type: 'float'));

        self::assertSame('amount', $error->fieldReference());
    }

    public function testIsValidation(): void
    {
        $error = $this->makeError(ErrorType::Validation);

        self::assertTrue($error->isValidation());
        self::assertFalse($error->isTechnical());
    }

    public function testIsTechnical(): void
    {
        $error = $this->makeError(ErrorType::Technical);

        self::assertTrue($error->isTechnical());
        self::assertFalse($error->isValidation());
    }

    private function makeError(ErrorType $type, ?FieldMetadata $field = null): EtlError
    {
        return new EtlError(
            errorCode: EtlErrorCode::UnexpectedError,
            type: $type,
            stage: ErrorStage::Pipeline,
            message: 'boom',
            field: $field,
        );
    }
}
