<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class UuidOrderedTimeGeneratorTest extends TestCase
{
    public function testUuidGeneratorGeneratesInLegacyMode(): void
    {
        $em = Mockery::mock(EntityManager::class);
        $entity = new Entity();
        $generator = new UuidOrderedTimeGenerator();

        $uuid = $generator->generate($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame(1, $uuid->getVersion());
    }

    public function testUuidGeneratorGenerates(): void
    {
        $em = Mockery::mock(EntityManager::class);
        $entity = new Entity();
        $generator = new UuidOrderedTimeGenerator();

        $uuid = $generator->generateId($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame(1, $uuid->getVersion());
    }
}
