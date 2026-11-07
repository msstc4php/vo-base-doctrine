<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use MSSTC4PHP\ValueObject\Identity\EmailAddress;
use Override;

final class EmailAddressType extends AbstractStringVoType
{
    public const string NAME = 'vo_email_address';

    #[Override]
    protected function valueObjectClass(): string
    {
        return EmailAddress::class;
    }
}
