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
        $apo13 = Domain::where('code', 'APO13')->first(); // Domain Baru
        $bai06 = Domain::where('code', 'BAI06')->first();
        $dss01 = Domain::where('code', 'DSS01')->first(); // Domain Baru
        $mea01 = Domain::where('code', 'MEA01')->first();

        $questions = [
            // ==========================================
            // --- PERTANYAAN UNTUK APO12 (Risk) ---
            // ==========================================
            [
                'domain_id' => $apo12 ? $apo12->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'APO12.01',
                'description' => 'Mengumpulkan data dan mengidentifikasi risiko TI secara rutin dan terstruktur.'
            ],
            [
                'domain_id' => $apo12 ? $apo12->id : null, 
                'capability_level' => 3, 
                'activity_code' => 'APO12.02',
                'description' => 'Menganalisa dan mengevaluasi risiko TI yang telah diidentifikasi secara berkala.'
            ],
            [
                'domain_id' => $apo12 ? $apo12->id : null, 
                'capability_level' => 4, 
                'activity_code' => 'APO12.03',
                'description' => 'Memelihara profil risiko TI dan memastikannya selaras dengan profil risiko perusahaan.'
            ],

            // ==========================================
            // --- PERTANYAAN UNTUK APO13 (Security) ---
            // ==========================================
            [
                'domain_id' => $apo13 ? $apo13->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'APO13.01',
                'description' => 'Membangun dan memelihara Sistem Manajemen Keamanan Informasi (ISMS) perusahaan.'
            ],
            [
                'domain_id' => $apo13 ? $apo13->id : null, 
                'capability_level' => 3, 
                'activity_code' => 'APO13.02',
                'description' => 'Mendefinisikan dan mengelola rencana penanganan risiko keamanan informasi.'
            ],
            [
                'domain_id' => $apo13 ? $apo13->id : null, 
                'capability_level' => 4, 
                'activity_code' => 'APO13.03',
                'description' => 'Memantau dan meninjau keefektifan sistem keamanan informasi secara proaktif.'
            ],

            // ==========================================
            // --- PERTANYAAN UNTUK BAI06 (IT Changes) ---
            // ==========================================
            [
                'domain_id' => $bai06 ? $bai06->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'BAI06.01',
                'description' => 'Mengevaluasi, memprioritaskan, dan mengotorisasi semua permintaan perubahan TI.'
            ],
            [
                'domain_id' => $bai06 ? $bai06->id : null, 
                'capability_level' => 3, 
                'activity_code' => 'BAI06.02',
                'description' => 'Mengelola perubahan darurat (Emergency Changes) dengan prosedur yang terdokumentasi.'
            ],
            [
                'domain_id' => $bai06 ? $bai06->id : null, 
                'capability_level' => 4, 
                'activity_code' => 'BAI06.03',
                'description' => 'Melacak status perubahan TI dan melaporkannya kepada manajemen tingkat atas.'
            ],

            // ==========================================
            // --- PERTANYAAN UNTUK DSS01 (Operations) ---
            // ==========================================
            [
                'domain_id' => $dss01 ? $dss01->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'DSS01.01',
                'description' => 'Melaksanakan prosedur operasional TI sesuai dengan jadwal yang telah ditentukan.'
            ],
            [
                'domain_id' => $dss01 ? $dss01->id : null, 
                'capability_level' => 3, 
                'activity_code' => 'DSS01.02',
                'description' => 'Memantau infrastruktur TI dan memastikan ketersediaan layanan operasional.'
            ],
            [
                'domain_id' => $dss01 ? $dss01->id : null, 
                'capability_level' => 4, 
                'activity_code' => 'DSS01.03',
                'description' => 'Mengelola fasilitas fisik dan lingkungan kerja TI agar tetap aman dan terkendali.'
            ],

            // ==========================================
            // --- PERTANYAAN UNTUK MEA01 (Monitoring) ---
            // ==========================================
            [
                'domain_id' => $mea01 ? $mea01->id : null, 
                'capability_level' => 2, 
                'activity_code' => 'MEA01.01',
                'description' => 'Menetapkan pendekatan pemantauan (monitoring) kinerja dan kesesuaian TI.'
            ],
            [
                'domain_id' => $mea01 ? $mea01->id : null, 
                'capability_level' => 3, 
                'activity_code' => 'MEA01.02',
                'description' => 'Menetapkan target kinerja dan metrik evaluasi yang selaras dengan tujuan bisnis.'
            ],
            [
                'domain_id' => $mea01 ? $mea01->id : null, 
                'capability_level' => 4, 
                'activity_code' => 'MEA01.03',
                'description' => 'Melaporkan kinerja TI dan merekomendasikan tindakan perbaikan kepada manajemen.'
            ],
        ];

        // Masukkan semua ke database (hanya jika domainnya ada)
        foreach ($questions as $q) {
            if ($q['domain_id'] !== null) {
                // Gunakan updateOrCreate agar kalau di-seed ulang tidak dobel datanya
                CobitQuestion::updateOrCreate(
                    ['activity_code' => $q['activity_code']], // Cek berdasarkan kode aktivitas
                    $q
                );
            }
        }
    }
}