# Changelog

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
