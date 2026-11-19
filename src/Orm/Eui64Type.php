<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Network\Eui64;
use Override;

final class Eui64Type extends AbstractStringVoType
{
    public const string NAME = 'vo_eui64';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Eui64::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 23;
    }
}
