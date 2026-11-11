<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Orm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Geo\GeoCoordinates;
use MSSTC4PHP\ValueObject\Payload\GeoCoordinatesCodec;
use MSSTC4PHP\ValueObject\Payload\PayloadCodec;
use Override;

/**
 * Maps {@see GeoCoordinates} to a single JSON text column ({"lat":..,"lng":..}).
 * When the sub-fields must be queryable, map the value object as an
 * `#[ORM\Embeddable]` with a column per coordinate instead of using this type.
 *
 * @extends AbstractJsonVoType<GeoCoordinates>
 */
final class GeoCoordinatesType extends AbstractJsonVoType
{
    public const string NAME = 'vo_geo_coordinates';

    private static ?GeoCoordinatesCodec $codec = null;

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?GeoCoordinates
    {
        return parent::convertToPHPValue($value, $platform);
    }

    /**
     * @return PayloadCodec<GeoCoordinates>
     */
    #[Override]
    protected function codec(): PayloadCodec
    {
        return self::$codec ??= new GeoCoordinatesCodec();
    }
}
