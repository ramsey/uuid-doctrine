<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

use function method_exists;

class UuidV7GeneratorTest extends TestCase
{
    public function testUuidV7GeneratorGeneratesInLegacyMode(): void
    {
        if (!method_exists(Uuid::class, 'uuid7')) {
            $this->markTestSkipped('Uuid::uuid7() is not available in the installed version of ramsey/uuid');
        }

        $em = Mockery::mock(EntityManager::class);
        $entity = new Entity();
        $generator = new UuidV7Generator();

        $uuid = $generator->generate($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
    }

    public function testUuidV7GeneratorGenerates(): void
    {
        if (!method_exists(Uuid::class, 'uuid7')) {
            $this->markTestSkipped('Uuid::uuid7() is not available in the installed version of ramsey/uuid');
        }

        $em = Mockery::mock(EntityManager::class);
        $entity = new Entity();
        $generator = new UuidV7Generator();

        $uuid = $generator->generateId($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
    }

    public function testUuidV7GeneratorThrowsExceptionWhenUuid7NotAvailable(): void
    {
        if (method_exists(Uuid::class, 'uuid7')) {
            $this->markTestSkipped('Uuid::uuid7() available in the installed version of ramsey/uuid');
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Uuid::uuid7() from ramsey/uuid is not available on PHP <8.0');

        new UuidV7Generator();
    }
}
