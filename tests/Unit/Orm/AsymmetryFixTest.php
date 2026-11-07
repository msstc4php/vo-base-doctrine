<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Tests\Unit\Orm;

use Brick\Money\Context\CustomContext;
use Brick\Money\Context\DefaultContext;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use InvalidArgumentException;
use JsonException;
use MSSTC4PHP\ValueObject\Geo\GeoCoordinates;
use MSSTC4PHP\ValueObject\Geo\Latitude;
use MSSTC4PHP\ValueObject\Geo\Longitude;
use MSSTC4PHP\ValueObject\Primitive\BoundedString;
use MSSTC4PHP\ValueObject\Primitive\IntRange;
use MSSTC4PHP\ValueObject\Temporal\DateRange;
use MSSTC4PHP\ValueObjectDoctrineBridge\Orm\AbstractJsonVoType;
use MSSTC4PHP\ValueObjectDoctrineBridge\Orm\BoundedStringType;
use MSSTC4PHP\ValueObjectDoctrineBridge\Orm\BrickMoneyType;
use MSSTC4PHP\ValueObjectDoctrineBridge\Orm\DateRangeType;
use MSSTC4PHP\ValueObjectDoctrineBridge\Orm\GeoCoordinatesType;
use MSSTC4PHP\ValueObjectDoctrineBridge\Orm\IntRangeType;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\BoundedStringCodec;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\DateRangeCodec;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\GeoCoordinatesCodec;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\IntRangeCodec;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(BrickMoneyType::class)]
#[CoversClass(DateRangeType::class)]
#[CoversClass(DateRangeCodec::class)]
#[CoversClass(GeoCoordinatesType::class)]
#[CoversClass(BoundedStringCodec::class)]
#[CoversClass(IntRangeCodec::class)]
#[CoversClass(BoundedStringType::class)]
#[CoversClass(IntRangeType::class)]
#[CoversClass(AbstractJsonVoType::class)]
#[CoversClass(GeoCoordinatesCodec::class)]
final class AsymmetryFixTest extends TestCase
{
    private SQLitePlatform $platform;

    #[Override]
    protected function setUp(): void
    {
        $this->platform = new SQLitePlatform();
    }

    /**
     * @return iterable<string, array{Money}>
     */
    public static function moneyProvider(): iterable
    {
        yield 'default USD' => [Money::of('1.23', 'USD')];
        yield 'default JPY' => [Money::of('1234', 'JPY')];
        yield 'default IQD' => [Money::of('1.234', 'IQD')];
        // The regression: written happily, then unreadable.
        yield 'custom scale 4' => [Money::of('1.2345', 'USD', new CustomContext(4))];
        // Trailing zeros are why AutoContext was not an option on the read side.
        yield 'trailing zeros' => [Money::of('1.2300', 'USD', new CustomContext(4))];
        yield 'negative' => [Money::of('-0.05', 'USD')];
    }

    #[DataProvider('moneyProvider')]
    public function testMoneyRoundTripKeepsValueAndScale(Money $money): void
    {
        $type = new BrickMoneyType();

        $back = $type->convertToPHPValue($type->convertToDatabaseValue($money, $this->platform), $this->platform);

        self::assertInstanceOf(Money::class, $back);
        self::assertTrue($money->isEqualTo($back));
        self::assertSame($money->getAmount()->getScale(), $back->getAmount()->getScale());
    }

    /**
     * Values written before the fix carry the currency's own scale; they must
     * still read back, and with their DefaultContext identity intact so the
     * base money mapping keeps accepting them.
     */
    public function testMoneyWrittenBeforeTheFixStillReads(): void
    {
        $back = new BrickMoneyType()->convertToPHPValue('USD 1.23', $this->platform);

        self::assertInstanceOf(Money::class, $back);
        self::assertInstanceOf(DefaultContext::class, $back->getContext());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function malformedMoneyProvider(): iterable
    {
        yield 'currency only' => ['USD'];
        yield 'amount only' => ['1.23'];
        yield 'empty' => [''];
    }

    #[DataProvider('malformedMoneyProvider')]
    public function testMalformedMoneyColumnIsRejected(string $column): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BrickMoneyType()->convertToPHPValue($column, $this->platform);
    }

    public function testUnknownCurrencyIsNotSwallowed(): void
    {
        $this->expectException(UnknownCurrencyException::class);

        new BrickMoneyType()->convertToPHPValue('CNH 1.23', $this->platform);
    }

    /**
     * @return iterable<string, array{DateRange}>
     */
    public static function dateRangeProvider(): iterable
    {
        $moscow = new DateTimeZone('Europe/Moscow');
        $ny = new DateTimeZone('America/New_York');
        $utc = new DateTimeZone('UTC');

        yield 'microseconds and named zone' => [new DateRange(
            new DateTimeImmutable('2026-08-22 10:00:00.123456', $moscow),
            new DateTimeImmutable('2026-08-23 10:00:00.654321', $moscow),
        )];
        // Both instants of the repeated hour. The zone-name form restores the
        // first for either, shifting the second back by an hour.
        yield 'first 01:30 of the fall-back' => [new DateRange(
            new DateTimeImmutable('2026-11-01 05:30:00.123456', $utc)->setTimezone($ny),
            new DateTimeImmutable('2026-11-02 05:30:00.000000', $utc)->setTimezone($ny),
        )];
        yield 'second 01:30 of the fall-back' => [new DateRange(
            new DateTimeImmutable('2026-11-01 06:30:00.123456', $utc)->setTimezone($ny),
            new DateTimeImmutable('2026-11-02 06:30:00.000000', $utc)->setTimezone($ny),
        )];
        yield 'canonical whole seconds in UTC' => [new DateRange(
            new DateTimeImmutable('2026-01-01 00:00:00', $utc),
            new DateTimeImmutable('2026-12-31 23:59:59', $utc),
        )];
    }

    #[DataProvider('dateRangeProvider')]
    public function testDateRangeRoundTripIsIdentity(DateRange $range): void
    {
        $type = new DateRangeType();

        $back = $type->convertToPHPValue($type->convertToDatabaseValue($range, $this->platform), $this->platform);

        self::assertInstanceOf(DateRange::class, $back);
        self::assertTrue($range->equals($back));
        self::assertSame($range->start->getTimestamp(), $back->start->getTimestamp());
        self::assertSame($range->start->format('u'), $back->start->format('u'));
    }

    public function testDateRangeRejectsPayloadWithoutEnd(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DateRangeType()->convertToPHPValue('{"start":"2026-01-01T00:00:00.000000+00:00"}', $this->platform);
    }

    public function testDateRangeRejectsGarbage(): void
    {
        try {
            new DateRangeType()->convertToPHPValue('not json', $this->platform);
            self::fail('expected an exception');
        } catch (InvalidArgumentException $exception) {
            self::assertInstanceOf(JsonException::class, $exception->getPrevious());
            self::assertStringNotContainsString('not json', $exception->getMessage());
        }
    }

    /**
     * GeoCoordinates is the third composite value object; it was checked rather
     * than assumed, and its float JSON form loses nothing at full precision.
     */
    public function testGeoCoordinatesIsUnaffected(): void
    {
        $type = new GeoCoordinatesType();
        $coordinates = new GeoCoordinates(Latitude::from(-89.999999), Longitude::from(179.999999));

        $back = $type->convertToPHPValue($type->convertToDatabaseValue($coordinates, $this->platform), $this->platform);

        self::assertInstanceOf(GeoCoordinates::class, $back);
        self::assertTrue($coordinates->equals($back));
    }

    public function testMalformedGeoJsonFailsWithAMappingError(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GeoCoordinatesType()->convertToPHPValue('{"lat":"north"}', $this->platform);
    }

    public function testJsonScalarIsNotACompositePayload(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GeoCoordinatesType()->convertToPHPValue('"just a string"', $this->platform);
    }

    public function testStoredJsonIsUnchanged(): void
    {
        self::assertSame(
            '{"lat":55.751244,"lng":37.618423}',
            new GeoCoordinatesType()->convertToDatabaseValue(GeoCoordinates::fromFloats(55.751244, 37.618423), $this->platform),
        );
        self::assertSame(
            '{"start":"2026-11-07T10:00:00.123456+03:00","end":"2026-11-08T10:00:00.654321+03:00"}',
            new DateRangeType()->convertToDatabaseValue(new DateRange(
                new DateTimeImmutable('2026-11-07T10:00:00.123456+03:00'),
                new DateTimeImmutable('2026-11-08T10:00:00.654321+03:00'),
            ), $this->platform),
        );
        self::assertSame('{"min":-5,"max":5}', new IntRangeType()->convertToDatabaseValue(new IntRange(-5, 5), $this->platform));
        self::assertSame(
            '{"value":"abc","minLength":0,"maxLength":null}',
            new BoundedStringType()->convertToDatabaseValue(new BoundedString('abc'), $this->platform),
        );
    }

    public function testInvalidUtf8CannotBeWrittenAsJson(): void
    {
        try {
            new BoundedStringType()->convertToDatabaseValue(new BoundedString("\xFF"), $this->platform);
            self::fail('expected an exception');
        } catch (InvalidArgumentException $exception) {
            self::assertInstanceOf(JsonException::class, $exception->getPrevious());
        }
    }
}
