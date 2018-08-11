<?php
namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\TestCase;

class UuidOrderedTimeGeneratorTest extends TestCase
{
    /**
     * @var \Ramsey\Uuid\Doctrine\TestEntityManager
     */
    private $em;

    /**
     * @var \Doctrine\ORM\Mapping\Entity
     */
    private $entity;

    /**
     * @var \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator
     */
    private $generator;

    protected function setUp()
    {
        $this->em = new TestEntityManager();
        $this->entity = new Entity();
        $this->generator = new UuidOrderedTimeGenerator();
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator::generate
     * @covers \Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator::__construct
     */
    public function testUuidGeneratorGenerates()
    {
        $uuid = $this->generator->generate($this->em, $this->entity);

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        $this->assertEquals(1, $uuid->getVersion());
    }
}
