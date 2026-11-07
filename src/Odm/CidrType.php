<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use MSSTC4PHP\ValueObject\Network\Cidr;
use Override;

final class CidrType extends AbstractStringVoType
{
    public const string NAME = 'vo_cidr';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Cidr::class;
    }
}
