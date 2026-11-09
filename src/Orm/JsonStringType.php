<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Format\JsonString;
use Override;

final class JsonStringType extends AbstractTextVoType
{
    public const string NAME = 'vo_json_string';

    #[Override]
    protected function valueObjectClass(): string
    {
        return JsonString::class;
    }
}
