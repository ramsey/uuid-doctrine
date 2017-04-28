<?php
namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use Ramsey\Uuid\UuidInterface;

class UuidOrderedTimeGeneratorTest extends \PHPUnit_Framework_TestCase
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
     */
    public function testUuidGeneratorGenerates()
    {
        $uuid = $this->generator->generate($this->em, $this->entity);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertEquals(1, $uuid->getVersion());
    }
}
