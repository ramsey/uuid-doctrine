<?php
namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\Tests\DBAL\Mocks\MockPlatform;
use Ramsey\Uuid\Uuid;

class UuidBinaryTypeTest extends \PHPUnit_Framework_TestCase
{
    private $platform;
    private $type;

    public static function setUpBeforeClass()
    {
        if (class_exists('Doctrine\\DBAL\\Types\\Type')) {
            Type::addType('uuid_binary', 'Ramsey\Uuid\Doctrine\UuidBinaryType');
        }
    }

    protected function setUp()
    {
        $this->platform = $this->getPlatformMock();
        $this->platform->expects($this->any())
            ->method('getBinaryTypeDeclarationSQLSnippet')
            ->will($this->returnValue('DUMMYBINARY(16)'));
        $this->platform->expects($this->any())
            ->method('getBlobTypeDeclarationSQL')
            ->will($this->returnValue('DUMMYBLOB(16)'));

        $this->type = Type::getType('uuid_binary');
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::convertToDatabaseValue
     */
    public function testUuidConvertsToDatabaseValue()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');
        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::convertToDatabaseValue
     */
    public function testStringUuidConvertsToDatabaseValue()
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $expected = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');
        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::convertToDatabaseValue
     */
    public function testInvalidUuidConversionForDatabaseValue()
    {
        $this->setExpectedException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToDatabaseValue('abcdefg', $this->platform);
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::convertToDatabaseValue
     */
    public function testNullConversionForDatabaseValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::convertToPHPValue
     */
    public function testUuidConvertsToPHPValue()
    {
        $uuid = $this->type->convertToPHPValue(hex2bin('ff6f8cb0c57d11e19b210800200c9a66'), $this->platform);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::convertToPHPValue
     */
    public function testInvalidUuidConversionForPHPValue()
    {
        $this->setExpectedException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToPHPValue('abcdefg', $this->platform);
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::convertToPHPValue
     */
    public function testNullConversionForPHPValue()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::convertToPHPValue
     */
    public function testReturnValueIfUuidForPHPValue()
    {
        $uuid = Uuid::uuid4();
        $this->assertSame($uuid, $this->type->convertToPHPValue($uuid, $this->platform));
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('uuid_binary', $this->type->getName());
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::getSqlDeclaration
     */
    public function testGetGuidTypeDeclarationSQLWithDoctrineDbal25()
    {
        if (!\method_exists($this->platform, 'getBinaryTypeDeclarationSQL')) {
            $this->markTestSkipped('running with doctrine/dbal < 2.5.0');
        }

        $this->assertEquals('DUMMYBINARY(16)', $this->type->getSqlDeclaration(array('length' => 16), $this->platform));
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::getSqlDeclaration
     */
    public function testGetGuidTypeDeclarationSQLWithDoctrineDbal23()
    {
        if (\method_exists($this->platform, 'getBinaryTypeDeclarationSQL')) {
            $this->markTestSkipped('running with doctrine/dbal >= 2.5.0');
        }

        $this->assertEquals('DUMMYBLOB(16)', $this->type->getSqlDeclaration(array('length' => 16), $this->platform));
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidBinaryType::requiresSQLCommentHint
     */
    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPlatformMock()
    {
        return $this->getMockBuilder('Doctrine\DBAL\Platforms\AbstractPlatform')
            ->setMethods(array(
                'getBinaryTypeDeclarationSQLSnippet',
                'getBlobTypeDeclarationSQL'
            ))
            ->getMockForAbstractClass();
    }
}
