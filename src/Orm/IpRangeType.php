<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Orm;

use MSSTC4PHP\ValueObject\Network\IpRange;
use Override;

final class IpRangeType extends AbstractStringVoType
{
    public const string NAME = 'vo_ip_range';

    #[Override]
    protected function valueObjectClass(): string
    {
        return IpRange::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 79;
    }
}
