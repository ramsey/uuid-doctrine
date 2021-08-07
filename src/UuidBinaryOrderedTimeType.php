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

use InvalidArgumentException;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

/**
 * Field type mapping for the Doctrine Database Abstraction Layer (DBAL).
 *
 * UUID fields will be stored as a binary in the database and converted back to
 * the Uuid value object when querying.
 */
class UuidBinaryOrderedTimeType extends Type
{
    /**
     * @var string
     */
    const NAME = 'uuid_binary_ordered_time';

    /**
    * @var string
    */
    const ASSERT_FORMAT = 'UuidV1';

    /**
     * @var UuidFactory|null
     */
    private $factory;

    /**
     * @var OrderedTimeCodec
     */
    private $codec;

    /**
     * {@inheritdoc}
     *
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getBinaryTypeDeclarationSQL(
            [
                'length' => '16',
                'fixed' => true,
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param string|UuidInterface|null $value
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof UuidInterface) {
            return $value;
        }

        try {
            return $this->decode($value);
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param UuidInterface|string|null $value
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof UuidInterface) {
            return $this->encode($value);
        }

        try {
            if (is_string($value) || method_exists($value, '__toString')) {
                $uuid = $this->getUuidFactory()->fromString((string) $value);

                return $this->encode($uuid);
            }
        } catch (InvalidArgumentException $e) {
            // Ignore the exception and pass through.
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * Creates/returns a UuidFactory instance that uses a specific codec
     * that creates a binary that can be time-ordered
     *
     * @return UuidFactory|null
     */
    protected function getUuidFactory()
    {
        if (null === $this->factory) {
            $this->factory = new UuidFactory();
        }

        return $this->factory;
    }

    /**
     * @return OrderedTimeCodec
     */
    protected function getCodec()
    {
        if (null === $this->codec) {
            $this->codec = new OrderedTimeCodec(
                $this->getUuidFactory()->getUuidBuilder()
            );
        }

        return $this->codec;
    }

    /**
     * Using this type only makes sense with Uuid version 1 as this is the only
     * kind of UUID that can be time-ordered. Passing any other UUID into
     * this type is likely a mistake
     *
     * @param UuidInterface $value
     *
     * @throws ConversionException
     */
    private function assertUuidV1(UuidInterface $value)
    {
        if (1 !== $value->getVersion()) {
            throw ConversionException::conversionFailedFormat(
                $value->toString(),
                self::NAME,
                self::ASSERT_FORMAT
            );
        }
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return string
     *
     * @throws ConversionException
     */
    private function encode(UuidInterface $uuid)
    {
        $this->assertUuidV1($uuid);

        return $this->getCodec()->encodeBinary($uuid);
    }

    /**
     * @param string $bytes
     *
     * @return UuidInterface
     *
     * @throws ConversionException
     */
    private function decode($bytes)
    {
        try {
            $decoded = $this->getCodec()->decodeBytes($bytes);
        } catch (UnsupportedOperationException $e) {
            throw ConversionException::conversionFailedFormat(
                bin2hex($bytes),
                self::NAME,
                self::ASSERT_FORMAT
            );
        }

        $this->assertUuidV1($decoded);

        return $decoded;
    }
}
