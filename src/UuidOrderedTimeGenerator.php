<?php
namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;

class UuidOrderedTimeGenerator extends AbstractIdGenerator
{
    /**
     * @var \Ramsey\Uuid\UuidFactory
     */
    protected $factory;

    public function __construct()
    {
        $this->factory = clone Uuid::getFactory();

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
