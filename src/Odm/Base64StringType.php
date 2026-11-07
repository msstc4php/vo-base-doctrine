<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Odm;

use MSSTC4PHP\ValueObject\Format\Base64String;
use Override;

final class Base64StringType extends AbstractStringVoType
{
    public const string NAME = 'vo_base64_string';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Base64String::class;
    }
}
