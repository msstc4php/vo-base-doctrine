<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use MSSTC4PHP\ValueObject\Network\UrlScheme;
use Override;

final class UrlSchemeType extends AbstractStringVoType
{
    public const string NAME = 'vo_url_scheme';

    #[Override]
    protected function valueObjectClass(): string
    {
        return UrlScheme::class;
    }
}
