<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use Doctrine\ODM\MongoDB\Types\ClosureToPHP;
use Doctrine\ODM\MongoDB\Types\Type;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Doctrine\Shared\Payload\PayloadCodec;
use Override;

/**
 * Maps a composite value object to an embedded sub-document through its shared
 * payload codec. BSON stores arrays natively, so no JSON encoding is needed.
 *
 * @template TValue of ValueObject the composite value object this type maps
 */
abstract class AbstractArrayVoType extends Type
{
    use ClosureToPHP;

    /**
     * @return array<string, scalar|null>|null
     */
    #[Override]
    public function convertToDatabaseValue(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $codec = $this->codec();
        $expected = $codec->valueObjectClass();
        if (!$value instanceof $expected) {
            throw new InvalidArgumentException(\sprintf('Expected %s, got %s.', $expected, \get_debug_type($value)));
        }

        return $codec->encode($value);
    }

    /**
     * @return TValue|null
     *
     * @throws InvalidArgumentException when the stored payload does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value): ?ValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            throw new InvalidArgumentException(\sprintf('Expected a sub-document, got %s.', \get_debug_type($value)));
        }

        return $this->codec()->decode($value);
    }

    /**
     * @return PayloadCodec<TValue>
     */
    abstract protected function codec(): PayloadCodec;
}
