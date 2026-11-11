<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Geo\GeoCoordinates;
use MSSTC4PHP\ValueObject\Payload\GeoCoordinatesCodec;
use MSSTC4PHP\ValueObject\Payload\PayloadCodec;
use Override;

/**
 * Maps {@see GeoCoordinates} to an embedded sub-document {lat: float, lng: float}.
 *
 * @extends AbstractArrayVoType<GeoCoordinates>
 */
final class GeoCoordinatesType extends AbstractArrayVoType
{
    public const string NAME = 'vo_geo_coordinates';

    private static ?GeoCoordinatesCodec $codec = null;

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value): ?GeoCoordinates
    {
        return parent::convertToPHPValue($value);
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
