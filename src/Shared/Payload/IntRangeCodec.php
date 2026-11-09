<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Shared\Payload;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Primitive\IntRange;
use Override;

/**
 * {min: int, max: int}; IntRange::jsonSerialize() is lossless, so encode() delegates to it.
 *
 * @internal
 *
 * @implements PayloadCodec<IntRange>
 */
final readonly class IntRangeCodec implements PayloadCodec
{
    #[Override]
    public function valueObjectClass(): string
    {
        return IntRange::class;
    }

    /**
     * @return array{min: int, max: int}
     */
    #[Override]
    public function encode(ValueObject $value): array
    {
        return $value->jsonSerialize();
    }

    #[Override]
    public function decode(array $payload): IntRange
    {
        $min = $payload['min'] ?? null;
        $max = $payload['max'] ?? null;
        if (!\is_int($min) || !\is_int($max)) {
            throw new InvalidArgumentException('Expected an int-range payload with integer "min" and "max".');
        }

        return new IntRange($min, $max);
    }
}
