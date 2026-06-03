<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('audit_progresses', function (Blueprint $table) {
            $table->string('evidence_file')->nullable()->after('notes'); // 🔥 Tambah kolom ini
        });
    }
    public function down(): void {
        Schema::table('audit_progresses', function (Blueprint $table) {
            $table->dropColumn('evidence_file');
        });
    }
};