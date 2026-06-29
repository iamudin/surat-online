<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!config('modules.multisite_enabled')) {
            return;
        }
        $tables = [
            'wargas',
            'surat_types',
            'surat_fields',
            'surat_requests',
            'surat_request_data',
            'rts',
            'rws'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                    if (!Schema::hasColumn($table, 'tenant_id')) {
                        $table_blueprint->unsignedBigInteger('tenant_id')->nullable()->after('id')->index();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        if (!config('modules.multisite_enabled')) {
            return;
        }
        $tables = [
            'wargas',
            'surat_types',
            'surat_fields',
            'surat_requests',
            'surat_request_data',
            'rts',
            'rws'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table_blueprint) {
                    $table_blueprint->dropColumn('tenant_id');
                });
            }
        }
    }
};
