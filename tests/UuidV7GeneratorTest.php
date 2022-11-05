<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UuidV7GeneratorTest extends TestCase
{
    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidV7Generator::generate
     */
    public function testUuidV7GeneratorGenerates()
    {
        $em = new TestEntityManager();
        $entity = new Entity();
        if (! method_exists('Ramsey\Uuid\Uuid', 'uuid7')) {
            $this->markTestSkipped('Generator throws exception when uuid7 not available');
        }
        $generator = new UuidV7Generator();

        $uuid = $generator->generate($em, $entity);

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
    }

    /**
     * @covers \Ramsey\Uuid\Doctrine\UuidV7Generator::generate
     */
    public function testUuidV7GeneratorThrowsExceptionWhenUuid7NotAvailable()
    {
        if (method_exists('Ramsey\Uuid\Uuid', 'uuid7')) {
            $this->markTestSkipped('Generator works fine when uuid7 is available');
        }
        $this->expectException('RuntimeException');

        new UuidV7Generator();
    }
}
