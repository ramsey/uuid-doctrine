<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\TestCase;

class UuidGeneratorTest extends TestCase
{
    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidGenerator::generate
     */
    public function testUuidGeneratorGenerates()
    {
        $em = new TestEntityManager();
        $entity = new Entity();
        $generator = new UuidGenerator();

        $uuid = $generator->generate($em, $entity);

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
    }
}
