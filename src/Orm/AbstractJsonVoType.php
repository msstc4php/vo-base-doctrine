<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Orm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use JsonException;
use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Doctrine\Shared\Payload\PayloadCodec;
use Override;

/**
 * Maps a composite value object to one JSON text column through its shared
 * payload codec. When a sub-field must be queryable, map the value object as
 * an embeddable instead.
 *
 * @template TValue of ValueObject the composite value object this type maps
 */
abstract class AbstractJsonVoType extends Type
{
    /**
     * @param array<string, mixed> $column
     */
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    /**
     * @throws InvalidArgumentException when the value is of another class or cannot be encoded (invalid UTF-8)
     */
    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        $codec = $this->codec();
        $expected = $codec->valueObjectClass();
        if (!$value instanceof $expected) {
            throw new InvalidArgumentException(\sprintf('Expected %s, got %s.', $expected, \get_debug_type($value)));
        }

        try {
            return \json_encode($codec->encode($value), \JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException(\sprintf('Cannot encode %s as JSON (%s).', $expected, $exception->getMessage()), 0, $exception);
        }
    }

    /**
     * @return TValue|null
     *
     * @throws InvalidArgumentException when the stored payload does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw new InvalidArgumentException(\sprintf('Expected a string column value, got %s.', \get_debug_type($value)));
        }

        // The column content may be personal data (BoundedString), so messages name its shape, never its text.
        try {
            $decoded = \json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException(\sprintf('Expected a JSON object, got invalid JSON (%s).', $exception->getMessage()), 0, $exception);
        }

        if (!\is_array($decoded)) {
            throw new InvalidArgumentException(\sprintf('Expected a JSON object, got a JSON %s.', \get_debug_type($decoded)));
        }

        return $this->codec()->decode($decoded);
    }

    /**
     * @return PayloadCodec<TValue>
     */
    abstract protected function codec(): PayloadCodec;
}
