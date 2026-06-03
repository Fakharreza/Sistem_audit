<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Audit;
use App\Models\Domain;
use App\Models\CobitQuestion;
use App\Models\AuditResponse;
use Carbon\Carbon;

class DummyAuditSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 JANGAN LUPA GANTI EMAILNYA JADI EMAIL KAMU LAGI YA! 🔥
        $emailKamu = 'auditor@pal.co.id'; 
        
        $auditor = User::where('email', $emailKamu)->first();

        if (!$auditor) {
            $this->command->error("Gagal! Akun dengan email {$emailKamu} tidak ditemukan.");
            return;
        }

        $domains = Domain::all();
        if ($domains->isEmpty()) {
            $this->command->error('Domain kosong! Pastikan QuestionSeeder sudah dijalankan.');
            return;
        }
        $domainIds = $domains->pluck('id')->toArray();
        $questions = CobitQuestion::whereIn('domain_id', $domainIds)->get();

        // KITA BIKIN 25 DATA SEKALIGUS BIAR PAGINATION-NYA MANTAP!
        $totalDummy = 25; 

        for ($i = 1; $i <= $totalDummy; $i++) {
            // Acak Tahun, Bulan, Tanggal biar filternya kepake
            $year = rand(2024, 2026);
            $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
            $date = "{$year}-{$month}-{$day}";

            // Acak status (70% kemungkinan Completed, 30% Draft)
            $status = rand(1, 10) > 3 ? 'completed' : 'draft';
            
            // Bikin kode unik
            $code = "AUD-{$year}-" . str_pad($i, 3, '0', STR_PAD_LEFT) . "-" . rand(10,99);

            $audit = Audit::create([
                'user_id' => $auditor->id,
                'auditor_name' => $auditor->name,
                'audit_code' => $code,
                'audit_date' => $date,
                'status' => $status,
                'created_at' => Carbon::parse($date),
                'updated_at' => Carbon::parse($date)->addDays(rand(1, 5)),
            ]);

            // Tempelkan semua domain ke audit ini
            $audit->domains()->attach($domainIds);

            // Kalau statusnya completed, isi otomatis kuesionernya
            if ($status === 'completed') {
                $responses = [];
                
                foreach ($questions as $q) {
                    // Acak nilainya (0, 0.5, atau 1)
                    $scores = [0, 0.5, 1, 1, 1]; 
                    $score = $scores[array_rand($scores)];
                    
                    $responses[] = [
                        'audit_id' => $audit->id,
                        'cobit_question_id' => $q->id,
                        'score' => $score,
                        'notes' => "Catatan otomatis hasil inspeksi lapangan ke-{$i}. Temuan: " . ($score == 1 ? 'Sangat sesuai standar.' : 'Perlu tindakan korektif.'),
                        'evidence_file' => null,
                        'created_at' => Carbon::parse($date),
                        'updated_at' => Carbon::parse($date),
                    ];
                }
                
                // Simpan jawaban borongan
                AuditResponse::insert($responses);
            }
        }

        $this->command->info("🎉 BOOOM! Berhasil menyuntikkan {$totalDummy} Data Audit ke akun {$auditor->name}! Pagination dijamin melimpah ruah!");
    }
}