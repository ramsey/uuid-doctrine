<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\TestCase;

class UuidOrderedTimeGeneratorTest extends TestCase
{
    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator::generate
     * @covers \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator::__construct
     */
    public function testUuidGeneratorGenerates()
    {
        $em = new TestEntityManager();
        $entity = new Entity();
        $generator = new UuidOrderedTimeGenerator();

        $uuid = $generator->generate($em, $entity);

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        $this->assertSame(1, $uuid->getVersion());
    }
}
