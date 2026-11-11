# Changelog

## 1.1.0 - 2026-11-11

- Requires `msstc4php/vo-base` `^1.1`. The composite payload codecs now come from the core
  (`MSSTC4PHP\ValueObject\Payload`), shared with every framework bridge; the `@internal` `Shared\Payload` copies are
  gone. Stored formats are unchanged.
- A custom subclass of `Orm\AbstractJsonVoType` or `Odm\AbstractArrayVoType` now returns the core's
  `MSSTC4PHP\ValueObject\Payload\PayloadCodec` from `codec()`. The old return type was the `@internal`
  `Shared\Payload\PayloadCodec`, so no supported subclass breaks.
- Reading is stricter through the core codecs: a `DateRange` boundary must be an ISO 8601 instant with an offset (rows
  written by this package always are; hand-written rows like `"now"` or a wall time without offset now fail loudly).

## 1.0.0 - 2026-11-11

First stable release: from here on a breaking change needs 2.0. The Doctrine type names (`vo_*`) and the stored
formats are part of that promise.

- Requires `msstc4php/vo-base` `^1.0`. No code changes.

## 0.5.0 - 2026-11-09

**Changed (BC):** the package is renamed to the convention of third-party libraries with framework add-ons.

- Package `msstc4php/vo-base-doctrine-bridge` is now `msstc4php/vo-base-doctrine`; the GitHub repository moved to
  `msstc4php/vo-base-doctrine`.
- Namespace `MSSTC4PHP\ValueObjectDoctrineBridge\` is now `MSSTC4PHP\ValueObject\Doctrine\` (e.g.
  `MSSTC4PHP\ValueObject\Doctrine\Orm\TypeRegistrar`). Type names (`vo_*`) and stored formats are unchanged.
- Upgrading: replace the old package and its VCS URL with the new ones; `conflict` refuses to install both.

## 0.4.0 - 2026-11-07

- Requires `msstc4php/vo-base` ^0.4 (stricter validation: hydrating a stored value with a trailing newline or a
  URL with spaces now fails; find such rows with `tryFrom()` first).
- Column declarations of every ORM type are pinned by `SchemaDeclarationTest`.
- Composite types (`vo_geo_coordinates`, `vo_date_range`, `vo_int_range`, `vo_bounded_string`) share one payload
  codec per value object (`Shared\Payload`, `@internal`) across ORM and ODM, on new `AbstractJsonVoType` /
  `AbstractArrayVoType` bases; `PortType` extends `AbstractIntVoType`. Stored formats and public signatures are
  unchanged.
- **Fixed:** the ORM `vo_geo_coordinates` type validated nothing on read; a malformed payload now raises
  `InvalidArgumentException` instead of a `TypeError` or a coordinate built from garbage.
- **Changed (BC):** composite types raise `InvalidArgumentException` for every malformed payload: invalid JSON on
  read or a value JSON cannot encode on write (previously `JsonException`, kept as the previous exception), and an
  unparsable `DateRange` instant in both ORM and ODM (previously `DateMalformedStringException`). Messages no longer
  quote stored content.
- The ODM composite types' `convertToDatabaseValue()` is inherited, so its PHPDoc return type is the generic
  `array<string, scalar|null>|null` rather than the exact shape; the native signature is unchanged.
- **Changed (BC):** the ODM `vo_geo_coordinates` type no longer accepts numeric strings for `lat`/`lng`; it writes
  BSON doubles, so only hand-edited documents are affected.

## 0.3.0 - 2026-11-07

- `vo_ip_range` ORM and ODM types for `IpRange` (column length 79); requires `msstc4php/vo-base` ^0.3.

## 0.2.0 - 2026-11-07

- Requires `msstc4php/vo-base` ^0.2, whose ISO data matches brick/money 0.15.
- `brick/money` 0.15 and `brick/math` up to 1.0 are required; older releases are declared as conflicts because their
  ISO table disagrees with the core enum.
- Withdrawn currencies (ADP, BEF, ...) and `XCG` now map as money with their ISO minor units, following the core
  enum; codes without ISO minor units (`CNH`, `XEU`, ...) are still rejected.

## 0.1.0 - 2026-11-07

- Doctrine ORM and MongoDB ODM types and money embeddables for `msstc4php/vo-base`, relocated from the core.
- `brick/math` support starts at 0.14.2 and `brick/money` at 0.10.3: the embeddables rely on PascalCase
  `RoundingMode` cases.
