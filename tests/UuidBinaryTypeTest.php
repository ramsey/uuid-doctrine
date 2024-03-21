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

use function fopen;
use function fwrite;
use function hex2bin;
use function method_exists;
use function rewind;

class UuidBinaryTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Type::hasType('uuid_binary')) {
            Type::addType('uuid_binary', UuidBinaryType::class);
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
            ->allows('getBinaryTypeDeclarationSQLSnippet')
            ->andReturns('DUMMYBINARY(16)');

        return $platform;
    }

    private function getType(): Type
    {
        return Type::getType('uuid_binary');
    }

    public function testUuidConvertsToDatabaseValue(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');
        $actual = $this->getType()->convertToDatabaseValue($uuid, $this->getPlatform());

        $this->assertSame($expected, $actual);
    }

    public function testStringUuidConvertsToDatabaseValue(): void
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $expected = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');
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
        $uuid = $this->getType()->convertToPHPValue(hex2bin('ff6f8cb0c57d11e19b210800200c9a66'), $this->getPlatform());
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

    public function testResourceConvertsToPHPValue(): void
    {
        /** @var resource $stream */
        $stream = fopen('php://memory', 'r+b');
        /** @var string $binaryId */
        $binaryId = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');

        fwrite($stream, $binaryId);
        rewind($stream);

        $uuid = $this->getType()->convertToPHPValue($stream, $this->getPlatform());
        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testReturnValueIfUuid4ForPHPValue(): void
    {
        $uuid = Uuid::uuid4();
        $this->assertSame($uuid, $this->getType()->convertToPHPValue($uuid, $this->getPlatform()));
    }

    public function testReturnValueIfUuid7ForPHPValue(): void
    {
        if (!method_exists(Uuid::class, 'uuid7')) {
            $this->markTestSkipped('Uuid::uuid7() is not available in the installed version of ramsey/uuid');
        }

        $uuid = Uuid::uuid7();
        $this->assertSame($uuid, $this->getType()->convertToPHPValue($uuid, $this->getPlatform()));
    }

    public function testGetName(): void
    {
        $this->assertSame('uuid_binary', $this->getType()->getName());
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
        $this->assertEquals(16, $this->getType()->getBindingType());
    }
}
