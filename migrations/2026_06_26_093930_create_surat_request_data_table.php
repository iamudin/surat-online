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
        Schema::create('surat_request_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('surat_field_id')->constrained()->onDelete('cascade');
            $table->text('field_value')->nullable(); // For text, number, date, select
            $table->string('file_path')->nullable(); // For file uploads
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_request_data');
    }
};
