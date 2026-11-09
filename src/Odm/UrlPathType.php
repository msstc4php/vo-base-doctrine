<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Network\UrlPath;
use Override;

final class UrlPathType extends AbstractStringVoType
{
    public const string NAME = 'vo_url_path';

    #[Override]
    protected function valueObjectClass(): string
    {
        return UrlPath::class;
    }
}
