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
        Schema::create('surat_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_type_id')->constrained()->onDelete('cascade');
            $table->string('field_name');
            $table->string('field_type'); // text, number, date, select, file, textarea
            $table->json('field_options')->nullable(); // for select options
            $table->boolean('is_required')->default(true);
            $table->integer('order_num')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_fields');
    }
};
