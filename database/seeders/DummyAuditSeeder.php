<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Audit;
use App\Models\Domain;
use App\Models\CobitQuestion; // <-- FIX 1: Pakai model kamu
use App\Models\AuditResponse;

class DummyAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Tarik semua pertanyaan dari database (yang udah kamu buat pakai QuestionSeeder)
        $questions = CobitQuestion::all();

        // Kalau kosong, ingetin buat jalanin QuestionSeeder dulu
        if ($questions->isEmpty()) {
            $this->command->info('Harap jalankan QuestionSeeder terlebih dahulu!');
            return;
        }

        // BIKIN AUDIT BARU: Status otomatis 'Completed'
        $audit = Audit::create([
            'audit_code' => 'AUD-' . rand(1000, 9999) . '-TEST',
            'auditor_name' => 'Robot Auditor Seeder',
            'audit_date' => now()->toDateString(), 
            'status' => 'completed',
            'created_at' => now()->subDays(2),
            'updated_at' => now(),
        ]);

        // Hubungkan audit ini dengan domain yang ada (Jika pakai pivot table)
        try {
            $domains = Domain::all();
            $audit->domains()->attach($domains->pluck('id')->toArray());
        } catch (\Exception $e) {
            // Abaikan jika tidak pakai relasi attach domain
        }

        // ISI JAWABAN ACAK
        $scores = [0, 0, 0, 0.5, 0.5, 1]; // Banyakin nilai 0 biar muncul di meja Manajer

        foreach ($questions as $question) {
            $score = $scores[array_rand($scores)]; 
            $notes = '';

            if ($score == 0) {
                $notes = 'Kritikal: Belum ada prosedur/dokumen sama sekali terkait aktivitas ini di perusahaan.';
            } elseif ($score == 0.5) {
                $notes = 'Temuan: Aktivitas sudah dilakukan kadang-kadang, tapi belum jadi SOP baku yang didokumentasikan.';
            } else {
                $notes = 'Aman: Sudah berjalan 100% sesuai target COBIT 2019.';
            }

            // SIMPAN KE AUDIT RESPONSES
            AuditResponse::create([
                'audit_id' => $audit->id,
                'cobit_question_id' => $question->id, // <-- FIX 2: Sesuaikan nama kolommu!
                'score' => $score,
                'notes' => $notes,
            ]);
        }
    }
}