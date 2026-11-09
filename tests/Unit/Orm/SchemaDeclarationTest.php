<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Doctrine\Tests\Unit\Orm;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\Type;
use MSSTC4PHP\ValueObject\Doctrine\Orm\TypeRegistrar;
use Override;
use PHPUnit\Framework\Attributes\CoversNamespace;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * A column definition is a schema contract: changing a length or a column type
 * forces a migration on every consumer, so each declaration is pinned here.
 */
#[CoversNamespace('MSSTC4PHP\\ValueObject\\Doctrine\\Orm')]
final class SchemaDeclarationTest extends TestCase
{
    #[Override]
    public static function setUpBeforeClass(): void
    {
        TypeRegistrar::registerAll();
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function declarationProvider(): iterable
    {
        yield 'vo_base64_string' => ['vo_base64_string', 'CLOB'];
        yield 'vo_bic' => ['vo_bic', 'VARCHAR(11)'];
        yield 'vo_bounded_string' => ['vo_bounded_string', 'CLOB'];
        yield 'vo_cidr' => ['vo_cidr', 'VARCHAR(43)'];
        yield 'vo_class_string' => ['vo_class_string', 'VARCHAR(255)'];
        yield 'vo_credit_card_number' => ['vo_credit_card_number', 'VARCHAR(19)'];
        yield 'vo_date_range' => ['vo_date_range', 'CLOB'];
        yield 'vo_decimal' => ['vo_decimal', 'VARCHAR(65)'];
        yield 'vo_domain_name' => ['vo_domain_name', 'VARCHAR(253)'];
        yield 'vo_duration' => ['vo_duration', 'VARCHAR(40)'];
        yield 'vo_email_address' => ['vo_email_address', 'VARCHAR(255)'];
        yield 'vo_geo_coordinates' => ['vo_geo_coordinates', 'CLOB'];
        yield 'vo_hex_color' => ['vo_hex_color', 'VARCHAR(7)'];
        yield 'vo_hostname' => ['vo_hostname', 'VARCHAR(253)'];
        yield 'vo_iban' => ['vo_iban', 'VARCHAR(34)'];
        yield 'vo_int_range' => ['vo_int_range', 'CLOB'];
        yield 'vo_ip_address' => ['vo_ip_address', 'VARCHAR(45)'];
        yield 'vo_ip_range' => ['vo_ip_range', 'VARCHAR(79)'];
        yield 'vo_ipv4_address' => ['vo_ipv4_address', 'VARCHAR(15)'];
        yield 'vo_ipv6_address' => ['vo_ipv6_address', 'VARCHAR(45)'];
        yield 'vo_json_string' => ['vo_json_string', 'CLOB'];
        yield 'vo_locale' => ['vo_locale', 'VARCHAR(10)'];
        yield 'vo_mac_address' => ['vo_mac_address', 'VARCHAR(17)'];
        yield 'vo_money' => ['vo_money', 'VARCHAR(40)'];
        yield 'vo_non_empty_string' => ['vo_non_empty_string', 'VARCHAR(255)'];
        yield 'vo_non_negative_int' => ['vo_non_negative_int', 'INTEGER'];
        yield 'vo_percentage' => ['vo_percentage', 'DOUBLE PRECISION'];
        yield 'vo_phone_number' => ['vo_phone_number', 'VARCHAR(16)'];
        yield 'vo_port' => ['vo_port', 'INTEGER'];
        yield 'vo_positive_int' => ['vo_positive_int', 'INTEGER'];
        yield 'vo_semver' => ['vo_semver', 'VARCHAR(255)'];
        yield 'vo_slug' => ['vo_slug', 'VARCHAR(255)'];
        yield 'vo_time_of_day' => ['vo_time_of_day', 'VARCHAR(8)'];
        yield 'vo_uri' => ['vo_uri', 'VARCHAR(2048)'];
        yield 'vo_url' => ['vo_url', 'VARCHAR(2048)'];
        yield 'vo_url_path' => ['vo_url_path', 'VARCHAR(2048)'];
        yield 'vo_url_scheme' => ['vo_url_scheme', 'VARCHAR(32)'];
        yield 'vo_uuid' => ['vo_uuid', 'VARCHAR(36)'];
    }

    #[DataProvider('declarationProvider')]
    public function testDeclarationIsStable(string $name, string $expected): void
    {
        self::assertSame($expected, Type::getType($name)->getSQLDeclaration([], new SQLitePlatform()));
    }

    public function testEveryRegisteredTypeIsPinned(): void
    {
        $pinned = \array_map(static fn (array $row): string => $row[0], \iterator_to_array(self::declarationProvider()));

        self::assertEqualsCanonicalizing(\array_keys(TypeRegistrar::types()), \array_values($pinned));
    }
}
