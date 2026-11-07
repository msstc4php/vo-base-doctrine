<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use MSSTC4PHP\ValueObject\Network\Url;
use Override;

final class UrlType extends AbstractStringVoType
{
    public const string NAME = 'vo_url';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Url::class;
    }
}
