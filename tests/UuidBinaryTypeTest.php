<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UuidBinaryTypeTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();

        if (class_exists('Doctrine\DBAL\Types\Type') && !Type::hasType('uuid_binary')) {
            Type::addType('uuid_binary', 'Ramsey\Uuid\Doctrine\UuidBinaryType');
        }
    }

    protected function getPlatform()
    {
        $platform = $this->getPlatformMock();
        $platform->shouldAllowMockingProtectedMethods();
        $platform
            ->shouldReceive('getBinaryTypeDeclarationSQLSnippet')
            ->andReturn('DUMMYBINARY(16)');

        return $platform;
    }

    protected function getType()
    {
        return Type::getType('uuid_binary');
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::convertToDatabaseValue
     */
    public function testUuidConvertsToDatabaseValue()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::convertToDatabaseValue
     */
    public function testStringUuidConvertsToDatabaseValue()
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $expected = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::convertToDatabaseValue
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
    public function testInvalidValueTypeConversionForDatabaseValue()
    {
        if (!method_exists($this, 'expectException')) {
            $this->markTestSkipped('This version of PHPUnit does not have expectException()');
        }

        $this->expectException('Doctrine\\DBAL\\Types\\ConversionException');

        $this->getType()->convertToDatabaseValue(false, $this->getPlatform());
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::convertToDatabaseValue
     */
    public function testNullConversionForDatabaseValue()
    {
        $this->assertNull($this->getType()->convertToDatabaseValue(null, $this->getPlatform()));
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::convertToPHPValue
     */
    public function testUuidConvertsToPHPValue()
    {
        $uuid = $this->getType()->convertToPHPValue(hex2bin('ff6f8cb0c57d11e19b210800200c9a66'), $this->getPlatform());
        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::convertToPHPValue
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
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::convertToPHPValue
     */
    public function testNullConversionForPHPValue()
    {
        $this->assertNull($this->getType()->convertToPHPValue(null, $this->getPlatform()));
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::convertToPHPValue
     */
    public function testReturnValueIfUuidForPHPValue()
    {
        $uuid = Uuid::uuid4();
        $this->assertSame($uuid, $this->getType()->convertToPHPValue($uuid, $this->getPlatform()));
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('uuid_binary', $this->getType()->getName());
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::getSqlDeclaration
     */
    public function testGetGuidTypeDeclarationSQL()
    {
        $this->assertEquals(
            'DUMMYBINARY(16)',
            $this->getType()->getSqlDeclaration(['length' => 36], $this->getPlatform())
        );
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidBinaryType::requiresSQLCommentHint
     */
    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->getType()->requiresSQLCommentHint($this->getPlatform()));
    }

    /**
     * @return AbstractPlatform & MockInterface
     */
    private function getPlatformMock()
    {
        return Mockery::mock('Doctrine\DBAL\Platforms\AbstractPlatform')->makePartial();
    }
}
