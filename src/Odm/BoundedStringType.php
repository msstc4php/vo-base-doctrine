<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Payload\BoundedStringCodec;
use MSSTC4PHP\ValueObject\Payload\PayloadCodec;
use MSSTC4PHP\ValueObject\Primitive\BoundedString;
use Override;

/**
 * Maps {@see BoundedString} to an embedded sub-document carrying all three
 * fields; the bounds are part of its identity.
 *
 * @extends AbstractArrayVoType<BoundedString>
 */
final class BoundedStringType extends AbstractArrayVoType
{
    public const string NAME = 'vo_bounded_string';

    private static ?BoundedStringCodec $codec = null;

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value): ?BoundedString
    {
        return parent::convertToPHPValue($value);
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
