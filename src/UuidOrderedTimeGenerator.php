<?php
namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\UuidFactory;

class UuidOrderedTimeGenerator extends AbstractIdGenerator
{
    /**
     * @var \Ramsey\Uuid\UuidFactory
     */
    protected $factory;

    public function __construct()
    {
        $this->factory = new UuidFactory();

        $codec = new OrderedTimeCodec(
            $this->factory->getUuidBuilder()
        );

        $this->factory->setCodec($codec);
    }

    /**
     * Generates an identifier for an entity.
     *
     * @param EntityManager|EntityManager  $em
     * @param \Doctrine\ORM\Mapping\Entity $entity
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function generate(EntityManager $em, $entity)
    {
        return $this->factory->uuid1();
    }
}
