<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Domain;
use App\Models\CobitQuestion;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Cari ID domain berdasarkan kodenya
        $apo12 = Domain::where('code', 'APO12')->first();
        $bai06 = Domain::where('code', 'BAI06')->first();
        $mea01 = Domain::where('code', 'MEA01')->first();

        $questions = [
            // --- PERTANYAAN UNTUK APO12 ---
            [
                'domain_id' => $apo12 ? $apo12->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'APO12.01',
                'description' => 'Mengumpulkan data dan mengidentifikasi risiko TI secara rutin dan terstruktur.'
            ],
            [
                'domain_id' => $apo12 ? $apo12->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'APO12.02',
                'description' => 'Menganalisa dan mengevaluasi risiko TI yang telah diidentifikasi.'
            ],
            [
                'domain_id' => $apo12 ? $apo12->id : null, 
                'capability_level' => 3, 
                'activity_code' => 'APO12.03',
                'description' => 'Memelihara profil risiko TI dan memastikannya selaras dengan profil risiko perusahaan.'
            ],

            // --- PERTANYAAN UNTUK BAI06 ---
            [
                'domain_id' => $bai06 ? $bai06->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'BAI06.01',
                'description' => 'Mengevaluasi, memprioritaskan, dan mengotorisasi permintaan perubahan TI.'
            ],
            [
                'domain_id' => $bai06 ? $bai06->id : null, 
                'capability_level' => 3, 
                'activity_code' => 'BAI06.02',
                'description' => 'Melacak status perubahan TI dan melaporkannya kepada manajemen.'
            ],

            // --- PERTANYAAN UNTUK MEA01 ---
            [
                'domain_id' => $mea01 ? $mea01->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'MEA01.01',
                'description' => 'Menetapkan pendekatan pemantauan (monitoring) kinerja dan kesesuaian TI.'
            ],
        ];

        // Masukkan semua ke database (hanya jika domainnya ada)
        foreach ($questions as $q) {
            if ($q['domain_id'] !== null) {
                CobitQuestion::create($q);
            }
        }
    }
}