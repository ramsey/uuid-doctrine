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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * UUID generator for the Doctrine ORM.
 */
class UuidGenerator extends AbstractIdGenerator
{
    /**
     * Generate an identifier
     *
     * @param EntityManagerInterface $em
     * @param Entity $entity
     *
     * @return UuidInterface
     *
     * @throws Exception
     *
     * @deprecated as per parent. Accepts any EntityManagerInterface for maximum compatibility on PHP 7.4+.
     * @see UuidGenerator::generateId()
     */
    public function generate(EntityManagerInterface $em, $entity): UuidInterface
    {
        return Uuid::uuid4();
    }

    /**
     * Generate an identifier
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
        return Uuid::uuid4();
    }
}
