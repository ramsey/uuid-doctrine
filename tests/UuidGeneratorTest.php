<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class UuidGeneratorTest extends TestCase
{
    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidGenerator::generate
     */
    public function testUuidGeneratorGeneratesInLegacyMode(): void
    {
        $em = new TestEntityManager();
        $entity = new Entity();
        $generator = new UuidGenerator();

        $uuid = $generator->generate($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidGenerator::generateId
     */
    public function testUuidGeneratorGenerates(): void
    {
        $em = new TestEntityManager();
        $entity = new Entity();
        $generator = new UuidGenerator();

        $uuid = $generator->generateId($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
    }
}
