<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UuidTypeTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();

        if (class_exists('Doctrine\DBAL\Types\Type') && !Type::hasType('uuid')) {
            Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');
        }
    }

    protected function getPlatform()
    {
        $platform = $this->getPlatformMock();
        $platform->shouldAllowMockingProtectedMethods();
        $platform
            ->shouldReceive('getGuidTypeDeclarationSQL')
            ->andReturn('DUMMYVARCHAR()');

        return $platform;
    }

    protected function getType()
    {
        return Type::getType('uuid');
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testUuidConvertsToDatabaseValue()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = $uuid->toString();
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testUuidInterfaceConvertsToDatabaseValue()
    {
        $uuid = \Mockery::mock('Ramsey\\Uuid\\Uuid');

        $uuid
            ->shouldReceive('__toString')
            ->once()
            ->andReturn('foo');

        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame('foo', $actual);
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testUuidStringConvertsToDatabaseValue()
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame($uuid, $actual);
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testInvalidUuidConversionForDatabaseValue()
    {
        if (!method_exists($this, 'expectException')) {
            $this->markTestSkipped('This version of PHPUnit does not have expectException()');
        }

        $this->expectException('Doctrine\\DBAL\\Types\\ConversionException');

        $this->getType()->convertToDatabaseValue('abcdefg', $this->getPlatform());
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testNullConversionForDatabaseValue()
    {
        $this->assertNull($this->getType()->convertToDatabaseValue(null, $this->getPlatform()));
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testUuidInterfaceConvertsToPHPValue()
    {
        $uuid = \Mockery::mock('Ramsey\\Uuid\\Uuid');

        $actual = $this->getType()->convertToPHPValue($uuid, $this->getPlatform());

        $this->assertSame($uuid, $actual);
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testUuidConvertsToPHPValue()
    {
        $uuid = $this->getType()->convertToPHPValue('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $this->getPlatform());
        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        $this->assertSame('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testInvalidUuidConversionForPHPValue()
    {
        if (!method_exists($this, 'expectException')) {
            $this->markTestSkipped('This version of PHPUnit does not have expectException()');
        }

        $this->expectException('Doctrine\\DBAL\\Types\\ConversionException');

        $this->getType()->convertToPHPValue('abcdefg', $this->getPlatform());
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testNullConversionForPHPValue()
    {
        $this->assertNull($this->getType()->convertToPHPValue(null, $this->getPlatform()));
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testReturnValueIfUuidForPHPValue()
    {
        $uuid = Uuid::uuid4();
        $this->assertSame($uuid, $this->getType()->convertToPHPValue($uuid, $this->getPlatform()));
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::getName
     */
    public function testGetName()
    {
        $this->assertSame('uuid', $this->getType()->getName());
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::getSqlDeclaration
     */
    public function testGetGuidTypeDeclarationSQL()
    {
        $this->assertSame(
            'DUMMYVARCHAR()',
            $this->getType()->getSqlDeclaration(['length' => 36], $this->getPlatform())
        );
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::requiresSQLCommentHint
     */
    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->getType()->requiresSQLCommentHint($this->getPlatform()));
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidType::getMappedDatabaseTypes
     */
    public function testGetMappedDatabaseTypes()
    {
        $this->assertSame(['uuid'], $this->getType()->getMappedDatabaseTypes($this->getPlatformMock()));
    }

    /**
     * @return AbstractPlatform & MockInterface
     */
    private function getPlatformMock()
    {
        return Mockery::mock('Doctrine\DBAL\Platforms\AbstractPlatform')->makePartial();
    }
}
