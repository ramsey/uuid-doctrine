<?php

namespace Ramsey\Uuid\Doctrine;

use InvalidArgumentException;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
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
     * @param array                                     $fieldDeclaration
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getBinaryTypeDeclarationSQL(
            array(
                'length' => '16',
                'fixed' => true,
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param string|null                               $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Uuid) {
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
     * @param UuidInterface|null                           $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Uuid) {
            return $this->encode($value);
        }

        try {
            $uuid = $this->getUuidFactory()->fromString($value);
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }

        return $this->encode($uuid);
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
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return boolean
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * Creates/returns a UuidFactory instance that uses a specific codec
     * that creates a binary that can be time-ordered
     *
     * @return null|UuidFactory
     */
    private function getUuidFactory()
    {
        if (null === $this->factory) {
            $this->factory = new UuidFactory();
        }

        return $this->factory;
    }

    private function getCodec()
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
     * @throws ConversionException
     */
    private function assertUuidV1(UuidInterface $value)
    {
        if (1 !== $value->getVersion()) {
            throw ConversionException::conversionFailed(
                $value->toString(),
                self::NAME
            );
        }
    }

    private function encode(UuidInterface $uuid)
    {
        $this->assertUuidV1($uuid);

        return $this->getCodec()->encodeBinary($uuid);
    }

    private function decode($bytes)
    {
        $decoded = $this->getCodec()->decodeBytes($bytes);

        $this->assertUuidV1($decoded);

        return $decoded;
    }
}
