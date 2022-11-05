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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

/**
 * UUID version7 generator for the Doctrine ORM.
 */
class UuidV7Generator extends AbstractIdGenerator
{
    public function __construct()
    {
        if (! method_exists('Ramsey\Uuid\Uuid', 'uuid7')) {
            throw new RuntimeException('Uuid version 7 not available. Upgrade your PHP version');
        }
    }

    /**
     * Generate an identifier
     *
     * @param EntityManager $em
     * @param Entity $entity
     *
     * @return UuidInterface
     *
     * @throws Exception
     */
    public function generate(EntityManager $em, $entity)
    {
        return Uuid::uuid7();
    }
}
