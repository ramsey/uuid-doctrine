<?php

/**
 * This file is part of the ramsey/uuid-doctrine library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Exception;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

class UuidOrderedTimeGenerator extends AbstractIdGenerator
{
    /**
     * @var UuidFactory
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
     * @param EntityManagerInterface $em
     * @param Entity $entity
     *
     * @return UuidInterface
     *
     * @throws Exception
     *
     * @deprecated as per parent. Accepts any EntityManagerInterface for maximum compatibility on PHP 7.4+.
     * @see UuidOrderedTimeGenerator::generateId()
     */
    public function generate(EntityManagerInterface $em, $entity): UuidInterface
    {
        return $this->factory->uuid1();
    }

    /**
     * Generates an identifier for an entity.
     *
     * @param EntityManagerInterface $em
     * @param Entity $entity
     *
     * @return UuidInterface
     *
     * @throws Exception
     */
    public function generateId(EntityManagerInterface $em, $entity): UuidInterface
    {
        return $this->factory->uuid1();
    }
}
