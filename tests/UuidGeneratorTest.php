<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\TestCase;

class UuidGeneratorTest extends TestCase
{
    private $em;
    private $entity;
    private $generator;

    protected function setUp()
    {
        $this->em = new TestEntityManager();
        $this->entity = new Entity();
        $this->generator = new UuidGenerator();
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidGenerator::generate
     */
    public function testUuidGeneratorGenerates()
    {
        $uuid = $this->generator->generate($this->em, $this->entity);

        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $uuid);
    }
}
