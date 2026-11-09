<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Format\SemVer;
use Override;

final class SemVerType extends AbstractStringVoType
{
    public const string NAME = 'vo_semver';

    #[Override]
    protected function valueObjectClass(): string
    {
        return SemVer::class;
    }
}
