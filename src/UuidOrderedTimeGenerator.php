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
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

use function assert;

class UuidOrderedTimeGenerator extends AbstractIdGenerator
{
    protected UuidFactory $factory;

    public function __construct()
    {
        $factory = clone Uuid::getFactory();
        assert($factory instanceof UuidFactory);

        $this->factory = $factory;

        $codec = new OrderedTimeCodec(
            $this->factory->getUuidBuilder(),
        );

        $this->factory->setCodec($codec);
    }

    /**
     * Generates an identifier for an entity.
     *
     * @deprecated as per parent. Accepts any EntityManagerInterface for maximum compatibility on PHP 7.4+.
     *
     * @see UuidOrderedTimeGenerator::generateId()
     *
     * @param object | null $entity
     *
     * @throws Exception
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function generate(EntityManagerInterface $em, $entity): UuidInterface
    {
        return $this->factory->uuid1();
    }

    /**
     * Generates an identifier for an entity.
     *
     * @param object | null $entity
     *
     * @throws Exception
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function generateId(EntityManagerInterface $em, $entity): UuidInterface
    {
        return $this->factory->uuid1();
    }
}
