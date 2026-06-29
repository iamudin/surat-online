<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            // Attempt to drop unique if exists. To prevent error, check index first or just catch exception (but Laravel schema builder is sometimes strict).
            // Usually we just drop it directly.
            try {
                $table->dropUnique('wargas_nik_unique');
            } catch (\Exception $e) {
                // Ignore if it doesn't exist
            }
        });
        
        Schema::table('surat_types', function (Blueprint $table) {
            try {
                $table->dropUnique('surat_types_slug_unique');
            } catch (\Exception $e) {
                // Ignore
            }
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            try {
                $table->unique('nik');
            } catch (\Exception $e) {
                // Ignore
            }
        });
        Schema::table('surat_types', function (Blueprint $table) {
            try {
                $table->unique('slug');
            } catch (\Exception $e) {
                // Ignore
            }
        });
    }
};
