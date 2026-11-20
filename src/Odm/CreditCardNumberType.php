<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Finance\CreditCardNumber;
use Override;

/**
 * @deprecated since 1.3, store MSSTC4PHP\ValueObject\Financial\Card\PaymentCardNumber from msstc4php/vo-finance
 *             instead; removed no earlier than 2.0. Behaviour is unchanged.
 */
final class CreditCardNumberType extends AbstractStringVoType
{
    public const string NAME = 'vo_credit_card_number';

    #[Override]
    protected function valueObjectClass(): string
    {
        return CreditCardNumber::class;
    }
}
