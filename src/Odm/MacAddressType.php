<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Network\MacAddress;
use Override;

final class MacAddressType extends AbstractStringVoType
{
    public const string NAME = 'vo_mac_address';

    #[Override]
    protected function valueObjectClass(): string
    {
        return MacAddress::class;
    }
}
