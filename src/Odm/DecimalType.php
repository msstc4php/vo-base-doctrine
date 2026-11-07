<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use MSSTC4PHP\ValueObject\Finance\Decimal;
use Override;

final class DecimalType extends AbstractStringVoType
{
    public const string NAME = 'vo_decimal';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Decimal::class;
    }
}
