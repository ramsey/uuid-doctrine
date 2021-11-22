<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UuidBinaryOrderedTimeTypeTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();

        if (class_exists('Doctrine\DBAL\Types\Type') && !Type::hasType('uuid_binary_ordered_time')) {
            Type::addType('uuid_binary_ordered_time', 'Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType');
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
        return Type::getType('uuid_binary_ordered_time');
    }

    public function testGetName()
    {
        $this->assertEquals('uuid_binary_ordered_time', $this->getType()->getName());
    }

    public function testUuidConvertsToDatabaseValue()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = hex2bin('11e1c57dff6f8cb09b210800200c9a66');
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertEquals($expected, $actual);
    }

    public function testStringUuidConvertsToDatabaseValue()
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $expected = hex2bin('11e1c57dff6f8cb09b210800200c9a66');
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertEquals($expected, $actual);
    }

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

    public function testNullConversionForDatabaseValue()
    {
        $this->assertNull($this->getType()->convertToDatabaseValue(null, $this->getPlatform()));
    }

    public function testUuidConvertsToPHPValue()
    {
        $uuid = $this->getType()->convertToPHPValue(hex2bin('11e1c57dff6f8cb09b210800200c9a66'), $this->getPlatform());
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testInvalidUuidConversionForPHPValue()
    {
        if (!method_exists($this, 'expectException')) {
            $this->markTestSkipped('This version of PHPUnit does not have expectException()');
        }

        $this->expectException('Doctrine\\DBAL\\Types\\ConversionException');

        $this->getType()->convertToPHPValue('abcdefg', $this->getPlatform());
    }

    public function testUnsupportedUuidConversionToDatabaseValue()
    {
        if (!method_exists($this, 'expectException')) {
            $this->markTestSkipped('This version of PHPUnit does not have expectException()');
        }

        $this->expectException('Doctrine\\DBAL\\Types\\ConversionException');

        $this->getType()->convertToDatabaseValue(Uuid::uuid4(), $this->getPlatform());
    }

    public function testUnsupportedUuidConversionToPHPValue()
    {
        if (!method_exists($this, 'expectException')) {
            $this->markTestSkipped('This version of PHPUnit does not have expectException()');
        }

        $this->expectException('Doctrine\\DBAL\\Types\\ConversionException');

        $this->getType()->convertToPHPValue(hex2bin('01e1c57dff6f8cb09b210800200c9a66'), $this->getPlatform());
    }

    public function testNullConversionForPHPValue()
    {
        $this->assertNull($this->getType()->convertToPHPValue(null, $this->getPlatform()));
    }

    public function testReturnValueIfUuidForPHPValue()
    {
        $uuid = Uuid::uuid4();
        $this->assertSame($uuid, $this->getType()->convertToPHPValue($uuid, $this->getPlatform()));
    }

    public function testGetGuidTypeDeclarationSQL()
    {
        $this->assertEquals(
            'DUMMYBINARY(16)',
            $this->getType()->getSqlDeclaration(['length' => 36], $this->getPlatform())
        );
    }

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
