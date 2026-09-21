<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Tests\Factory;

use Ang3\Component\ETL\Contract\ContextInterface;
use Ang3\Component\ETL\Contract\Enum\ErrorStage;
use Ang3\Component\ETL\Contract\Enum\ErrorType;
use Ang3\Component\ETL\Contract\Enum\EtlErrorCode;
use Ang3\Component\ETL\Exception\FieldProcessingException;
use Ang3\Component\ETL\Exception\InvalidFieldValueException;
use Ang3\Component\ETL\Exception\MissingTransformerException;
use Ang3\Component\ETL\Factory\DefaultErrorFactory;
use Ang3\Component\ETL\Metadata\FieldMetadata;
use PHPUnit\Framework\TestCase;

final class DefaultErrorFactoryTest extends TestCase
{
    private DefaultErrorFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new DefaultErrorFactory();
    }

    public function testPlainExceptionBecomesAnUnexpectedTechnicalError(): void
    {
        $context = $this->contextAt(2, 10);
        $exception = new \RuntimeException('boom');

        $error = $this->factory->create($exception, ErrorStage::Writing, $context);

        self::assertSame(EtlErrorCode::UnexpectedError, $error->errorCode);
        self::assertSame(ErrorType::Technical, $error->type);
        self::assertSame(ErrorStage::Writing, $error->stage);
        self::assertSame('boom', $error->message);
        self::assertSame(['exception' => \RuntimeException::class], $error->errorParameters);
        self::assertNull($error->appCode);
        self::assertSame(2, $error->rowIndex);
        self::assertSame(10, $error->sourceLineNumber);
        self::assertSame(\RuntimeException::class, $error->exceptionClass);
    }

    public function testValidationExceptionIsClassifiedAsValidation(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new InvalidFieldValueException($field, 'nan');

        $error = $this->factory->create($exception, ErrorStage::FieldProcessing, $this->contextAt(null, null));

        self::assertSame(ErrorType::Validation, $error->type);
        self::assertSame(EtlErrorCode::InvalidFieldValue, $error->errorCode);
        self::assertSame(['field' => 'amount', 'type' => 'float', 'value' => 'nan'], $error->errorParameters);
    }

    public function testTechnicalExceptionIsClassifiedAsTechnical(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new MissingTransformerException($field);

        $error = $this->factory->create($exception, ErrorStage::FieldProcessing, $this->contextAt(null, null));

        self::assertSame(ErrorType::Technical, $error->type);
        self::assertSame(EtlErrorCode::MissingTransformer, $error->errorCode);
    }

    public function testStagedExceptionOverridesTheGivenFallbackStage(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new FieldProcessingException($field, ErrorStage::FieldResolution, 'raw', new \RuntimeException('inner'));

        $error = $this->factory->create($exception, ErrorStage::Writing, $this->contextAt(null, null));

        self::assertSame(ErrorStage::FieldResolution, $error->stage);
    }

    public function testNonStagedExceptionFallsBackToTheGivenStage(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new MissingTransformerException($field);

        $error = $this->factory->create($exception, ErrorStage::Writing, $this->contextAt(null, null));

        self::assertSame(ErrorStage::Writing, $error->stage);
    }

    public function testFindsTheEtlExceptionThroughThePreviousChain(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $inner = new MissingTransformerException($field);
        $outer = new \RuntimeException('outer', previous: $inner);

        $error = $this->factory->create($outer, ErrorStage::FieldProcessing, $this->contextAt(null, null));

        self::assertSame(EtlErrorCode::MissingTransformer, $error->errorCode);
        self::assertSame(ErrorType::Technical, $error->type);
        self::assertSame('outer', $error->message);
    }

    public function testFieldAndRawValueArePassedThrough(): void
    {
        $field = new FieldMetadata(reference: 'amount', type: 'float');
        $exception = new \RuntimeException('boom');

        $error = $this->factory->create($exception, ErrorStage::FieldProcessing, $this->contextAt(null, null), $field, 'raw-value');

        self::assertSame($field, $error->field);
        self::assertSame('raw-value', $error->rawValue);
    }

    private function contextAt(?int $rowIndex, ?int $sourceLineNumber): ContextInterface
    {
        $context = $this->createStub(ContextInterface::class);
        $context->method('rowIndex')->willReturn($rowIndex);
        $context->method('sourceLineNumber')->willReturn($sourceLineNumber);

        return $context;
    }
}
