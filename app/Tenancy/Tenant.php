<?php
namespace App\Tenancy;

use App\Models\Organization;

class Tenant
{
    protected static ?Organization $org = null;

    public static function set(Organization $org): void { self::$org = $org; }
    public static function get(): ?Organization { return self::$org; }
    public static function id(): ?int { return self::$org?->id; }
    public static function forget(): void { self::$org = null; }
}
