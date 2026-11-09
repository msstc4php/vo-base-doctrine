<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Orm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Doctrine\Shared\Payload\DateRangeCodec;
use MSSTC4PHP\ValueObject\Doctrine\Shared\Payload\PayloadCodec;
use MSSTC4PHP\ValueObject\Temporal\DateRange;
use Override;

/**
 * Maps {@see DateRange} to a single JSON text column ({"start":..,"end":..}).
 * When the endpoints must be queryable, map two DATETIME columns instead of
 * using this type.
 *
 * @extends AbstractJsonVoType<DateRange>
 */
final class DateRangeType extends AbstractJsonVoType
{
    public const string NAME = 'vo_date_range';

    private static ?DateRangeCodec $codec = null;

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateRange
    {
        return parent::convertToPHPValue($value, $platform);
    }

    /**
     * @return PayloadCodec<DateRange>
     */
    #[Override]
    protected function codec(): PayloadCodec
    {
        return self::$codec ??= new DateRangeCodec();
    }
}
