<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObjectDoctrineBridge\Shared\Payload;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Contract\ValueObject;

/**
 * Turns a composite value object into the flat payload both branches store
 * (a JSON column in ORM, a sub-document in ODM) and back. One codec per value
 * object keeps the two branches from drifting apart.
 *
 * @template TValue of ValueObject the composite value object this codec maps
 *
 * @internal shared by the ORM and ODM types; encode() trusts that the type checked the class
 */
interface PayloadCodec
{
    /**
     * @return class-string<TValue>
     */
    public function valueObjectClass(): string;

    /**
     * @param TValue $value
     *
     * @return array<string, scalar|null>
     */
    public function encode(ValueObject $value): array;

    /**
     * The payload comes from the database and has no trusted shape; decode()
     * narrows it.
     *
     * @param array<array-key, mixed> $payload
     *
     * @return TValue
     *
     * @throws InvalidArgumentException when the payload does not have the expected shape
     */
    public function decode(array $payload): ValueObject;
}
