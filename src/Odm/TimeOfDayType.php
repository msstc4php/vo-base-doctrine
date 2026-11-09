<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Temporal\TimeOfDay;
use Override;

final class TimeOfDayType extends AbstractStringVoType
{
    public const string NAME = 'vo_time_of_day';

    #[Override]
    protected function valueObjectClass(): string
    {
        return TimeOfDay::class;
    }
}
