<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Finance\Bic;
use Override;

final class BicType extends AbstractStringVoType
{
    public const string NAME = 'vo_bic';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Bic::class;
    }
}
