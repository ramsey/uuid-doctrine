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
    private $platform;

    /** @var UuidBinaryOrderedTimeType */
    private $type;

    public static function setUpBeforeClass()
    {
        if (class_exists('Doctrine\DBAL\Types\Type')) {
            Type::addType('uuid_binary_ordered_time', 'Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType');
        }
    }

    protected function setUp()
    {
        $this->platform = $this->getPlatformMock();
        $this->platform->shouldAllowMockingProtectedMethods();
        $this->platform
            ->shouldReceive('getBinaryTypeDeclarationSQLSnippet')
            ->andReturn('DUMMYBINARY(16)');

        $this->type = Type::getType('uuid_binary_ordered_time');
    }

    public function testGetName()
    {
        $this->assertEquals('uuid_binary_ordered_time', $this->type->getName());
    }

    public function testUuidConvertsToDatabaseValue()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = hex2bin('11e1c57dff6f8cb09b210800200c9a66');
        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    public function testStringUuidConvertsToDatabaseValue()
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $expected = hex2bin('11e1c57dff6f8cb09b210800200c9a66');
        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     */
    public function testInvalidUuidConversionForDatabaseValue()
    {
        $this->type->convertToDatabaseValue('abcdefg', $this->platform);
    }

    public function testNullConversionForDatabaseValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testUuidConvertsToPHPValue()
    {
        $uuid = $this->type->convertToPHPValue(hex2bin('11e1c57dff6f8cb09b210800200c9a66'), $this->platform);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     */
    public function testInvalidUuidConversionForPHPValue()
    {
        $this->type->convertToPHPValue('abcdefg', $this->platform);
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     */
    public function testUnsupportedUuidConversionToDatabaseValue()
    {
        $this->type->convertToDatabaseValue(Uuid::uuid4(), $this->platform);
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     * @dataProvider provideUnsupportedDatabaseValues
     * @param string $databaseValue
     */
    public function testUnsupportedUuidConversionToPHPValue($databaseValue)
    {
        $this->type->convertToPHPValue(hex2bin($databaseValue), $this->platform);
    }

    public function testNullConversionForPHPValue()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testReturnValueIfUuidForPHPValue()
    {
        $uuid = Uuid::uuid4();
        $this->assertSame($uuid, $this->type->convertToPHPValue($uuid, $this->platform));
    }

    public function testGetGuidTypeDeclarationSQL()
    {
        $this->assertEquals('DUMMYBINARY(16)', $this->type->getSqlDeclaration(['length' => 36], $this->platform));
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function provideUnsupportedDatabaseValues()
    {
        $values = [];

        $tail = '1e1c57dff6f8cb09b210800200c9a66';
        for ($i = 0; $i <= 9; $i++) {
            if (1 === $i) {
                continue;
            }
            $values["Packed UUID that begins with $i"] = [$i . $tail];
        }

        return $values;
    }

    /**
     * @return AbstractPlatform & MockInterface
     */
    private function getPlatformMock()
    {
        return Mockery::mock('Doctrine\DBAL\Platforms\AbstractPlatform')->makePartial();
    }
}
