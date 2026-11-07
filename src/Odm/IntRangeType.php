<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Primitive\IntRange;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\IntRangeCodec;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\PayloadCodec;
use Override;

/**
 * Maps {@see IntRange} to an embedded sub-document {min: int, max: int}.
 *
 * @extends AbstractArrayVoType<IntRange>
 */
final class IntRangeType extends AbstractArrayVoType
{
    public const string NAME = 'vo_int_range';

    private static ?IntRangeCodec $codec = null;

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value): ?IntRange
    {
        return parent::convertToPHPValue($value);
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
