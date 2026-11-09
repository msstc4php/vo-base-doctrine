<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Shared\Money;

use InvalidArgumentException;

/**
 * Raised when a value cannot be mapped by the money embeddables. Extends
 * {@see InvalidArgumentException} so callers catching that keep working, as
 * every type of this package throws that.
 */
final class MoneyMappingException extends InvalidArgumentException
{
}
