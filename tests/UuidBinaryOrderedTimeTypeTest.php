<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

use function hex2bin;

class UuidBinaryOrderedTimeTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Type::hasType('uuid_binary_ordered_time')) {
            Type::addType('uuid_binary_ordered_time', UuidBinaryOrderedTimeType::class);
        }
    }

    /**
     * @return MockInterface & AbstractPlatform
     */
    private function getPlatform(): MockInterface
    {
        $platform = Mockery::mock(AbstractPlatform::class)->makePartial();
        $platform->shouldAllowMockingProtectedMethods();
        $platform
            ->allows('getBinaryTypeDeclarationSQLSnippet')
            ->andReturns('DUMMYBINARY(16)');

        return $platform;
    }

    private function getType(): UuidBinaryOrderedTimeType
    {
        return Type::getType('uuid_binary_ordered_time');
    }

    public function testGetName(): void
    {
        $this->assertSame('uuid_binary_ordered_time', $this->getType()->getName());
    }

    public function testUuidConvertsToDatabaseValue(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = hex2bin('11e1c57dff6f8cb09b210800200c9a66');
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame($expected, $actual);
    }

    public function testStringUuidConvertsToDatabaseValue(): void
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $expected = hex2bin('11e1c57dff6f8cb09b210800200c9a66');
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame($expected, $actual);
    }

    public function testInvalidUuidConversionForDatabaseValue(): void
    {
        $this->expectException(ConversionException::class);

        $this->getType()->convertToDatabaseValue('abcdefg', $this->getPlatform());
    }

    public function testInvalidValueTypeConversionForDatabaseValue(): void
    {
        $this->expectException(ConversionException::class);

        $this->getType()->convertToDatabaseValue(false, $this->getPlatform());
    }

    public function testNullConversionForDatabaseValue(): void
    {
        $this->assertNull($this->getType()->convertToDatabaseValue(null, $this->getPlatform()));
    }

    public function testUuidConvertsToPHPValue(): void
    {
        $uuid = $this->getType()->convertToPHPValue(hex2bin('11e1c57dff6f8cb09b210800200c9a66'), $this->getPlatform());
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertSame('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testInvalidUuidConversionForPHPValue(): void
    {
        $this->expectException(ConversionException::class);

        $this->getType()->convertToPHPValue('abcdefg', $this->getPlatform());
    }

    public function testUnsupportedUuidConversionToDatabaseValue(): void
    {
        $this->expectException(ConversionException::class);

        $this->getType()->convertToDatabaseValue(Uuid::uuid4(), $this->getPlatform());
    }

    public function testUnsupportedUuidConversionToPHPValue(): void
    {
        $this->expectException(ConversionException::class);

        $this->getType()->convertToPHPValue(hex2bin('01e1c57dff6f8cb09b210800200c9a66'), $this->getPlatform());
    }

    public function testNullConversionForPHPValue(): void
    {
        $this->assertNull($this->getType()->convertToPHPValue(null, $this->getPlatform()));
    }

    public function testReturnValueIfUuidForPHPValue(): void
    {
        $uuid = Uuid::uuid4();
        $this->assertSame($uuid, $this->getType()->convertToPHPValue($uuid, $this->getPlatform()));
    }

    public function testGetGuidTypeDeclarationSQL(): void
    {
        $this->assertSame(
            'DUMMYBINARY(16)',
            $this->getType()->getSqlDeclaration(['length' => 36], $this->getPlatform()),
        );
    }

    public function testRequiresSQLCommentHint(): void
    {
        $this->assertTrue($this->getType()->requiresSQLCommentHint($this->getPlatform()));
    }

    public function testItReturnsAppropriateBindingType(): void
    {
        $this->assertEquals(ParameterType::BINARY, $this->getType()->getBindingType());
    }
}
