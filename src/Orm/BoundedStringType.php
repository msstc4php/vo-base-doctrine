<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Orm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Primitive\BoundedString;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\BoundedStringCodec;
use MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload\PayloadCodec;
use Override;

/**
 * Maps {@see BoundedString} to one JSON column carrying all three fields; a
 * scalar column would lose the bounds, which are part of its identity.
 *
 * @extends AbstractJsonVoType<BoundedString>
 */
final class BoundedStringType extends AbstractJsonVoType
{
    public const string NAME = 'vo_bounded_string';

    private static ?BoundedStringCodec $codec = null;

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?BoundedString
    {
        return parent::convertToPHPValue($value, $platform);
    }

    /**
     * @return PayloadCodec<BoundedString>
     */
    #[Override]
    protected function codec(): PayloadCodec
    {
        return self::$codec ??= new BoundedStringCodec();
    }
}
