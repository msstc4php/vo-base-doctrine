<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Shared\Payload;

use DateMalformedStringException;
use DateTimeImmutable;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Temporal\DateRange;
use Override;

/**
 * {start: string, end: string} of ISO 8601 instants.
 *
 * @internal
 *
 * @implements PayloadCodec<DateRange>
 */
final readonly class DateRangeCodec implements PayloadCodec
{
    /**
     * Microseconds plus a numeric offset. The core's jsonSerialize() uses ATOM,
     * which carries neither -- a DateRange with microseconds or a named zone was
     * written happily and came back different, equals() returning false.
     *
     * The offset is deliberate rather than the zone name: local time plus a zone
     * name is ambiguous in the repeated hour of a DST fall-back, and restoring
     * from it picks the first of the two instants. Measured on the second
     * 01:30:00.123456 in America/New_York, that shifts the value back one hour;
     * the offset form round-trips both instants exactly. DateRange::equals()
     * compares instants, so the offset is sufficient.
     */
    private const string BOUNDARY_FORMAT = 'Y-m-d\TH:i:s.uP';

    #[Override]
    public function valueObjectClass(): string
    {
        return DateRange::class;
    }

    /**
     * @return array{start: string, end: string}
     */
    #[Override]
    public function encode(ValueObject $value): array
    {
        return [
            'start' => $value->start->format(self::BOUNDARY_FORMAT),
            'end' => $value->end->format(self::BOUNDARY_FORMAT),
        ];
    }

    #[Override]
    public function decode(array $payload): DateRange
    {
        $start = $payload['start'] ?? null;
        $end = $payload['end'] ?? null;
        if (!\is_string($start) || !\is_string($end)) {
            throw new InvalidArgumentException('Expected a date-range payload with string "start" and "end".');
        }

        try {
            return new DateRange(new DateTimeImmutable($start), new DateTimeImmutable($end));
        } catch (DateMalformedStringException $exception) {
            throw new InvalidArgumentException('Expected ISO 8601 instants in "start" and "end".', 0, $exception);
        }
    }
}
