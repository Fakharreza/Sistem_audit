<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah fitur Soft Delete di tabel master Kriteria
        Schema::table('criteria', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 2. Tambah kolom fotokopi (snapshot) di tabel hasil SAW
        Schema::table('gap_evaluations', function (Blueprint $table) {
            $table->decimal('weight_snapshot', 4, 2)->nullable();
            $table->string('type_snapshot')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('criteria', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('gap_evaluations', function (Blueprint $table) {
            $table->dropColumn(['weight_snapshot', 'type_snapshot']);
        });
    }
};