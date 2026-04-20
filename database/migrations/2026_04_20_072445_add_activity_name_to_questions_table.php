<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cobit_questions', function (Blueprint $table) {
            // Nambahin nama aktivitas biar rapi kayak di Excel
            $table->string('activity_name')->nullable()->after('activity_code'); 
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('activity_name');
        });
    }
};