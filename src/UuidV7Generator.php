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

declare(strict_types=1);

namespace Ramsey\Uuid\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

use function method_exists;

/**
 * UUID version7 generator for the Doctrine ORM.
 */
class UuidV7Generator extends AbstractIdGenerator
{
    public function __construct()
    {
        if (!method_exists(Uuid::class, 'uuid7')) {
            throw new RuntimeException('Uuid::uuid7() from ramsey/uuid is not available on PHP <8.0');
        }
    }

    /**
     * Generate an identifier
     *
     * @deprecated as per parent. Accepts any EntityManagerInterface for maximum compatibility on PHP 7.4+.
     *
     * @see UuidV7Generator::generateId()
     *
     * @param object | null $entity*
     *
     * @throws Exception
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function generate(EntityManagerInterface $em, $entity): UuidInterface
    {
        return Uuid::uuid7();
    }

    /**
     * Generate an identifier
     *
     * @param object | null $entity
     *
     * @throws Exception
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function generateId(EntityManagerInterface $em, $entity): UuidInterface
    {
        return Uuid::uuid7();
    }
}
