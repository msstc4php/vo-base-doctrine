# Known issues

Things that cost time to rediscover.

## `Money::ofMinor()` ignores the context's scale

It divides by `10 ** isoMinorUnits` of the currency, whatever the context says.

```
ofMinor(12345, 'USD', new CustomContext(4))          => USD 123.4500   // wrong
of(BigDecimal::ofUnscaledValue(12345, 4), 'USD', …)  => USD 1.2345     // right
```

This is why `PreciseMoneyAmount` is a separate class and not a parameter of
`MoneyAmount`: the storage form and the reconstruction path both differ.

## Two relocated types wrote values they could not read

Both passed their canonical round-trip tests for months.

- **`BrickMoneyType`** wrote the full `BigDecimal` and read with the implicit
  `DefaultContext`, so any `Money` above the currency's scale stored fine and
  threw `RoundingNecessaryException` on the way out. Fixed by taking the scale
  back from the stored string. `DefaultContext` is kept when that scale equals
  the currency's ISO minor units, so ordinary amounts keep their context
  identity and the base money mapping still accepts them.
- **`DateRangeType`** serialised through the core's `jsonSerialize()`, which
  formats as ATOM — no microseconds, no zone. Fixed with `Y-m-d\TH:i:s.uP`.

  The **offset**, not the zone name: local time plus a zone name is ambiguous in
  the repeated hour of a DST fall-back. Measured on the second
  `01:30:00.123456` in `America/New_York`, restoring by zone name lands on the
  first instant and loses an hour.

`tests/{Orm,Odm}/WriteReadSymmetryTest.php` now sweeps every registry key with
a deliberately awkward value and refuses to run if a new type has no sample. It
found a third gap on its first execution — `DateRangeType` asserted its decoded
payload shape with a `@var` annotation instead of checking it, so a missing
`end` key raised a PHP warning rather than an exception.

## `AutoContext` strips trailing zeros and calls a deprecated method

`"USD 1.2300"` comes back at scale 2, and `AutoContext::applyTo()` calls
`BigDecimal::stripTrailingZeros()`, deprecated in brick/math. Both are why the
read path uses `CustomContext`. Tests that must construct an `AutoContext`
carry `#[IgnoreDeprecations]`.

## CLDR display digits are not ISO 4217 minor units

`symfony/intl` and the ISO table disagree for 15 currencies in current use (25 codes with historic ones):

```
AFN ALL COP HUF IDR IRR KPW LAK LBP MGA MMK SOS SYP YER       CLDR 0, ISO 2
IQD                                                            CLDR 0, ISO 3
MRO SLL STD TMM ZMK ZWD (withdrawn)                            CLDR 0, ISO 2
BEF GRD PTE TPE (withdrawn)                                    CLDR 2, ISO 0
```

Storing 1000 HUF through the display value writes 1000 minor units and reads
back 10.00 HUF. The core exposes both, named for what they are:
`displayFractionDigits()` and `isoMinorUnits()`. Money code uses the latter,
always.

## Red `IsoMinorUnitsParityTest` is usually a version skew

The test compares the core's table against brick/money's. Both repositories
resolve brick/money independently, so an ordinary `composer update` in one of
them turns it red with no data defect at all. The failure message carries the
installed version — check that first, then regenerate the core enum.

A coordinated bump that moves both tables together stays green here; the core's
`iso-minor-units.snapshot.json` is what catches that.

## `isoMinorUnits() + 2` exceeds the ISO 20022 ceiling for two currencies

`CLF` and `UYW` have four ISO digits, so the high-precision rule gives scale 6 —
above the ISO 20022 generic-amount ceiling of five. Accepted deliberately; the
rule is this project's, not a standard.

## A money field cannot be nullable

Doctrine instantiates an embeddable without the constructor, and for a NULL
column set `TypedNoDefaultPropertyAccessor` unsets the property instead of
assigning null. No exception at hydration — the object simply fails on first
access. Use the single-column `vo_money` or two plain columns for an optional
amount.

Related: hydration bypasses the constructor entirely, so the currency and range
validation guards the write path only.

## `DecimalType` declares VARCHAR(65) for an arbitrary-precision value

Pre-existing, out of this package's scope to change. A `Decimal` longer than 65
characters is accepted by the converter and will not fit the column.

## Rector's PHP 8.4 output can break deptrac

Rector's `php84` set rewrites `(new Foo($x))->bar()` into `new Foo($x)->bar()`,
which is valid PHP 8.4 but a syntax error to deptrac's parser. `php -l` and
PHPStan stay green, so the failure surfaces only in `make check`'s last step.
Avoid the construct; `MoneyContextRouter` drops reflection for this reason.

## Inherited Rector warnings

Three skip entries copied from the core are deprecated or never registered
(`ChangeOrIfContinueToMultiContinueRector`, `ReturnBinaryOrToEarlyReturnRector`,
`DisallowedEmptyRuleFixerRector`). Noise, not failures; kept so the two
repositories' configs stay line-comparable.

## `brick/money` and `brick/math` floor is 0.15

The core enum's ISO data comes from brick/money 0.15.2, which knows withdrawn codes and `XCG`. With brick/money
0.10-0.14 the core would accept `new MoneyAmount(100, CurrencyCode::ADP)` while `toBrickMoney()` failed later with
`UnknownCurrencyException`, so `composer.json` declares `conflict: brick/money <0.15` (and brick/math `<0.15`, which
brick/money 0.15 needs anyway). The ranges run to brick/math `^1.0`. `IsoMinorUnitsParityTest` therefore stays strict:
a code brick/money does not know must have `null` in the core. Codes without ISO minor units (`CNH`, `XEU`, ...) are
rejected by `MoneyScale`.

## `vo_ip_range` stores the canonical form

`IpRange` keeps its bounds in RFC 5952 form (lower-case, zero runs compressed), unlike `IpAddress` and `Cidr`, which
keep the input spelling. A range written as `2001:DB8::1-2001:DB8::FF` reads back as `2001:db8::1-2001:db8::ff`;
the ORM column length 79 is the longest canonical range (two 39-character bounds and the separator).

## Deliberately not built: Decimal128 money in ODM, a high-precision `vo_money`

- ODM `Decimal128` for money: rejected for parity with the ORM string form; `MongoDB\BSON\Decimal128` has no
  arithmetic in PHP, and server-side `$sum` already works on the embeddables' integer minor units. Revisit only for
  a concrete aggregation need on `vo_money` itself.
- A high-precision mode for the single-column `vo_money`: unnecessary, the column stores whatever scale was written
  and reads it back losslessly; `PreciseMoneyAmount` is the queryable high-precision form.

