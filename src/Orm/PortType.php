<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Orm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Network\Port;
use MSSTC4PHP\ValueObject\Support\AbstractIntValueObject;
use Override;

final class PortType extends AbstractIntVoType
{
    public const string NAME = 'vo_port';

    /**
     * @throws InvalidArgumentException when the stored value does not have the expected shape
     */
    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Port
    {
        $port = parent::convertToPHPValue($value, $platform);
        \assert(!$port instanceof AbstractIntValueObject || $port instanceof Port);

        return $port;
    }

    #[Override]
    protected function valueObjectClass(): string
    {
        return Port::class;
    }
}
