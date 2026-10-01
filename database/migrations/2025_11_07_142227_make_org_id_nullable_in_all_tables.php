<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            
            'developers',
            'partners',
            'customers',
            'devices',
            'leads',
            'mail_settings',
            'partner_commissions',
            'projects',
            'project_materials',
            'project_units',
            'sites',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'org_id')) {
                echo "🛠 Processing table: {$table}\n";

                try {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$table}_org_id_foreign`;");
                    echo "✅ Dropped foreign key on {$table}.org_id\n";
                } catch (\Throwable $e) {
                    echo "⚠️ No foreign key found on {$table}.org_id or already dropped.\n";
                }
 
                try {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `org_id` BIGINT NULL;");
                    echo "✅ Made {$table}.org_id nullable\n";
                } catch (\Throwable $e) {
                    echo "❌ Failed to modify {$table}.org_id: {$e->getMessage()}\n";
                }
            } else {
                echo "ℹ️ Skipping {$table}: no org_id column found.\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            
            'developers',
            'partners',
            'customers',
            'devices',
            'leads',
            'mail_settings',
            'partner_commissions',
            'projects',
            'project_materials',
            'project_units',
            'sites',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'org_id')) {
                try {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `org_id` BIGINT NOT NULL;");
                    echo "↩️ Reverted {$table}.org_id back to NOT NULL\n";
                } catch (\Throwable $e) {
                    echo "⚠️ Failed to revert {$table}.org_id: {$e->getMessage()}\n";
                }
            }
        }
    }
};
