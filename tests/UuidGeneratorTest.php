<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class UuidGeneratorTest extends TestCase
{
    public function testUuidGeneratorGeneratesInLegacyMode(): void
    {
        $em = Mockery::mock(EntityManager::class);
        $entity = new Entity();
        $generator = new UuidGenerator();

        $uuid = $generator->generate($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
    }

    public function testUuidGeneratorGenerates(): void
    {
        $em = Mockery::mock(EntityManager::class);
        $entity = new Entity();
        $generator = new UuidGenerator();

        $uuid = $generator->generateId($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
    }
}
