<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Temporal\DateRange;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\DateRangeCodec;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\PayloadCodec;
use Override;

/**
 * Maps {@see DateRange} to an embedded sub-document {start: string, end: string}
 * of ISO 8601 instants.
 *
 * @extends AbstractArrayVoType<DateRange>
 */
final class DateRangeType extends AbstractArrayVoType
{
    public const string NAME = 'vo_date_range';

    private static ?DateRangeCodec $codec = null;

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value): ?DateRange
    {
        return parent::convertToPHPValue($value);
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
