<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class UuidOrderedTimeGeneratorTest extends TestCase
{
    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator::generate
     * @covers \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator::__construct
     */
    public function testUuidGeneratorGeneratesInLegacyMode(): void
    {
        $em = new TestEntityManager();
        $entity = new Entity();
        $generator = new UuidOrderedTimeGenerator();

        $uuid = $generator->generate($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame(1, $uuid->getVersion());
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator::generateId
     * @covers \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator::__construct
     */
    public function testUuidGeneratorGenerates(): void
    {
        $em = new TestEntityManager();
        $entity = new Entity();
        $generator = new UuidOrderedTimeGenerator();

        $uuid = $generator->generateId($em, $entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame(1, $uuid->getVersion());
    }
}
