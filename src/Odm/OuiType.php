<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Network\Oui;
use Override;

final class OuiType extends AbstractStringVoType
{
    public const string NAME = 'vo_oui';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Oui::class;
    }
}
