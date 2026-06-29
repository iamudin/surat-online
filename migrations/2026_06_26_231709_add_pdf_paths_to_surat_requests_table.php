<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surat_requests', function (Blueprint $table) {
            $table->string('pdf_path')->nullable();
            $table->string('signed_pdf_path')->nullable();
            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_requests', function (Blueprint $table) {
            $table->dropColumn(['pdf_path', 'signed_pdf_path', 'is_signed', 'signed_at']);
        });
    }
};
