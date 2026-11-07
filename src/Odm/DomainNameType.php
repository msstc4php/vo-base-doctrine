<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use MSSTC4PHP\ValueObject\Network\DomainName;
use Override;

final class DomainNameType extends AbstractStringVoType
{
    public const string NAME = 'vo_domain_name';

    #[Override]
    protected function valueObjectClass(): string
    {
        return DomainName::class;
    }
}
