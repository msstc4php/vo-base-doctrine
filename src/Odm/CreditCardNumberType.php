<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Finance\CreditCardNumber;
use Override;

final class CreditCardNumberType extends AbstractStringVoType
{
    public const string NAME = 'vo_credit_card_number';

    #[Override]
    protected function valueObjectClass(): string
    {
        return CreditCardNumber::class;
    }
}
