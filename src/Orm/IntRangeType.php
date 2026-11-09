<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Orm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Doctrine\Shared\Payload\IntRangeCodec;
use MSSTC4PHP\ValueObject\Doctrine\Shared\Payload\PayloadCodec;
use MSSTC4PHP\ValueObject\Primitive\IntRange;
use Override;

/**
 * Maps {@see IntRange} to one JSON column {min: int, max: int}.
 *
 * @extends AbstractJsonVoType<IntRange>
 */
final class IntRangeType extends AbstractJsonVoType
{
    public const string NAME = 'vo_int_range';

    private static ?IntRangeCodec $codec = null;

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?IntRange
    {
        return parent::convertToPHPValue($value, $platform);
    }

    /**
     * @return PayloadCodec<IntRange>
     */
    #[Override]
    protected function codec(): PayloadCodec
    {
        return self::$codec ??= new IntRangeCodec();
    }
}
