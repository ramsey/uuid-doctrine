<?php
namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\Tests\DBAL\Mocks\MockPlatform;
use Ramsey\Uuid\Uuid;

class UuidGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $em;
    private $entity;
    private $generator;

    protected function setUp()
    {
        $this->em = new TestEntityManager();
        $this->entity = new \Doctrine\ORM\Mapping\Entity();
        $this->generator = new UuidGenerator();
    }

    /**
     * @covers Ramsey\Uuid\Doctrine\UuidGenerator::generate
     */
    public function testUuidGeneratorGenerates()
    {
        $uuid = $this->generator->generate($this->em, $this->entity);

        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $uuid);
    }
}
