<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Orm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Support\AbstractIntValueObject;
use Override;

/**
 * Maps any int-backed value object to an INTEGER column. A concrete subclass
 * only names its value-object class.
 */
abstract class AbstractIntVoType extends Type
{
    /**
     * @param array<string, mixed> $column
     */
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        $expected = $this->valueObjectClass();

        if (!$value instanceof $expected) {
            throw new InvalidArgumentException(\sprintf('Expected %s, got %s.', $expected, \get_debug_type($value)));
        }

        return $value->value;
    }

    /**
     * @throws InvalidArgumentException when the stored value is not numeric
     */
    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?AbstractIntValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_numeric($value)) {
            throw new InvalidArgumentException(\sprintf('Expected a numeric column value, got %s.', \get_debug_type($value)));
        }

        return ($this->valueObjectClass())::from((int) $value);
    }

    /**
     * @return class-string<AbstractIntValueObject>
     */
    abstract protected function valueObjectClass(): string;
}
