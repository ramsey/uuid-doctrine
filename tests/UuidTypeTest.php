<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Type::hasType('uuid')) {
            Type::addType('uuid', UuidType::class);
        }
    }

    /**
     * @return AbstractPlatform & MockInterface
     */
    private function getPlatform(): MockInterface
    {
        $platform = Mockery::mock(AbstractPlatform::class)->makePartial();
        $platform->shouldAllowMockingProtectedMethods();
        $platform
            ->allows('getGuidTypeDeclarationSQL')
            ->andReturns('DUMMYVARCHAR()');

        return $platform;
    }

    private function getType(): UuidType
    {
        return Type::getType('uuid');
    }

    public function testUuidConvertsToDatabaseValue(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = $uuid->toString();
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame($expected, $actual);
    }

    public function testUuidInterfaceConvertsToDatabaseValue(): void
    {
        $uuid = Mockery::mock(Uuid::class);
        $uuid->expects('__toString')->andReturns('foo');

        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame('foo', $actual);
    }

    public function testUuidStringConvertsToDatabaseValue(): void
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame($uuid, $actual);
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

    public function testUuidInterfaceConvertsToPHPValue(): void
    {
        $uuid = Mockery::mock(Uuid::class);

        $actual = $this->getType()->convertToPHPValue($uuid, $this->getPlatform());

        $this->assertSame($uuid, $actual);
    }

    public function testUuidConvertsToPHPValue(): void
    {
        $uuid = $this->getType()->convertToPHPValue('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $this->getPlatform());
        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testInvalidUuidConversionForPHPValue(): void
    {
        $this->expectException(ConversionException::class);

        $this->getType()->convertToPHPValue('abcdefg', $this->getPlatform());
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

    public function testGetName(): void
    {
        $this->assertSame('uuid', $this->getType()->getName());
    }

    public function testGetGuidTypeDeclarationSQL(): void
    {
        $this->assertSame(
            'DUMMYVARCHAR()',
            $this->getType()->getSqlDeclaration(['length' => 36], $this->getPlatform()),
        );
    }

    public function testRequiresSQLCommentHint(): void
    {
        $this->assertTrue($this->getType()->requiresSQLCommentHint($this->getPlatform()));
    }

    public function testGetMappedDatabaseTypes(): void
    {
        $this->assertSame(['uuid'], $this->getType()->getMappedDatabaseTypes($this->getPlatform()));
    }
}
