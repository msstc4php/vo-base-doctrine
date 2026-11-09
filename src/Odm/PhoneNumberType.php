<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Format\PhoneNumber;
use Override;

final class PhoneNumberType extends AbstractStringVoType
{
    public const string NAME = 'vo_phone_number';

    #[Override]
    protected function valueObjectClass(): string
    {
        return PhoneNumber::class;
    }
}
