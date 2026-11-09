<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Network\Hostname;
use Override;

final class HostnameType extends AbstractStringVoType
{
    public const string NAME = 'vo_hostname';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Hostname::class;
    }
}
