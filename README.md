# msstc4php/vo-base-doctrine-bridge

Doctrine ORM and MongoDB ODM types for [`msstc4php/vo-base`][core]:
38 field types per branch, plus two money embeddables.

No framework is a runtime dependency. Install the Doctrine stack you actually
use; the other branch never loads.

## Install

Neither this package nor the core is on Packagist yet, so register both GitHub repositories:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-base" },
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-base-doctrine-bridge" }
    ],
    "require": {
        "msstc4php/vo-base-doctrine-bridge": "^0.4"
    }
}
```

## Registering the types

Without Symfony, once at bootstrap — idempotent, safe in long-running workers:

```php
use MSSTC4PHP\ValueObjectDoctrineBridge\Orm\TypeRegistrar;

TypeRegistrar::registerAll();
```

With Symfony, map `TypeRegistrar::types()` under `doctrine.dbal.types` instead,
or install [`msstc4symfony/vo-base-bridge`][bridge], whose bundle does it.

The ODM branch has its own `Odm\TypeRegistrar` with the same API and the same
type names — the same 38 keys on both sides.

## Mapping a field

```php
#[ORM\Column(type: 'vo_email_address')]
private EmailAddress $email;

#[ORM\Column(type: 'vo_percentage')]
private Percentage $discount;

// Backed enums need no custom type.
#[ORM\Column(enumType: CurrencyCode::class)]
private CurrencyCode $currency;
```

## Money

Three mappings, for three different jobs.

```php
// One VARCHAR, any context, nullable-friendly. No SQL aggregation.
#[ORM\Column(type: 'vo_money', nullable: true)]
private ?Money $total;

// Two columns: minor units plus currency. Aggregates in SQL.
#[ORM\Embedded(class: MoneyAmount::class)]
private MoneyAmount $subtotal;

// Two columns at two extra digits, for intermediate results.
#[ORM\Embedded(class: PreciseMoneyAmount::class)]
private PreciseMoneyAmount $unitPrice;
```

```php
$amount = MoneyAmount::fromBrickMoney(Money::of('1.23', 'USD'));
$amount->minorUnits;          // 123
$amount->toDecimalString();   // "1.23" -- works without brick/money
$amount->toBrickMoney();      // Money, back again

PreciseMoneyAmount::fromMoneyAmount($amount)->unscaledValue;     // 12300
$precise->toMoneyAmount(RoundingMode::HalfEven)->minorUnits;     // rounding is explicit
```

**Choosing between them**, and the two things that will otherwise surprise you —
an embeddable field cannot be nullable, and SQL aggregation is only valid within
one currency — are in
[`.claude/docs/integrations.md`](.claude/docs/integrations.md).

## Scale comes from ISO 4217

The scale of a money column is `CurrencyCode::isoMinorUnits()`, never the CLDR
display digits. The two differ for 15 currencies in current use (25 codes with historic ones): 1000 HUF is 100000 minor
units by ISO and would be 1000 by CLDR, so mixing them shifts stored amounts by
two orders of magnitude. A contract test pins the core's table against
brick/money's.

## Development

```
make check   # php -l, PHPStan level max, cs-fixer, composer validate/audit, Rector, deptrac
make test    # PHPUnit
make fix     # cs-fixer + Rector, applied
```

[core]: https://github.com/msstc4php/vo-base
[bridge]: https://github.com/msstc4symfony/vo-base-bridge
