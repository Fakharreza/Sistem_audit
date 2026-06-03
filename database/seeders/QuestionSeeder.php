<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Domain;
use App\Models\CobitQuestion;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            'APO12' => 'Managed Risk',
            'APO13' => 'Managed Security',
            'BAI06' => 'Managed IT Changes',
            'DSS01' => 'Managed Operations',
            'MEA01' => 'Managed Performance and Conformance Monitoring'
        ];

        foreach ($domains as $code => $name) {
            $domain = Domain::firstOrCreate(
                ['code' => $code],
                ['name' => $name]
            );

            $filePath = database_path("seeders/csv/{$code}.csv");
            
            if (!file_exists($filePath)) {
                $this->command->error("File {$code}.csv tidak ditemukan! Lewati...");
                continue;
            }

            $file = fopen($filePath, 'r');
            $firstLine = fgets($file);
            $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
            rewind($file); 

            $currentLevel = null;
            $currentActivityCode = null;
            $currentActivityName = null;
            $count = 0;

            while (($row = fgetcsv($file, 4000, $delimiter)) !== false) {
                $colA = trim($row[0] ?? '');
                $colB = trim($row[1] ?? '');
                $colC = trim($row[2] ?? '');
                $colD = trim($row[3] ?? '');

                if (stripos($colA, 'Capability Level') !== false) {
                    $currentLevel = (int) filter_var($colA, FILTER_SANITIZE_NUMBER_INT);
                    continue; 
                }

                if (empty($colA) && !empty($colB) && str_starts_with($colB, $code)) {
                    $parts = explode(' ', $colB, 2);
                    $currentActivityCode = trim($parts[0]); 
                    $currentActivityName = trim($parts[1] ?? ''); 
                    continue; 
                }

                if (empty($colA) && empty($colB) && !empty($colC) && !empty($colD)) {
                    
                    // 🔥 PANGGIL FUNGSI LOGIKA PINTAR DI SINI!
                    $hint = $this->generateEvidenceHint($colD);

                    CobitQuestion::updateOrCreate(
                        [
                            'domain_id' => $domain->id,
                            'activity_code' => $currentActivityCode,
                            'description' => $colD, 
                        ],
                        [
                            'capability_level' => $currentLevel,
                            'activity_name' => $currentActivityName, 
                            'evidence_hint' => $hint, // 🔥 SIMPAN HINT KE DATABASE
                        ]
                    );
                    $count++;
                }
            }
            fclose($file);
            
            $this->command->info("Berhasil import {$count} pertanyaan untuk domain {$code}!");
        }
    }

    private function generateEvidenceHint($description)
    {
        $desc = strtolower($description);
        $hints = [];

        if (str_contains($desc, 'kebijakan') || str_contains($desc, 'pedoman') || str_contains($desc, 'metode') || str_contains($desc, 'ditetapkan') || str_contains($desc, 'prosedur') || str_contains($desc, 'aturan')) {
            $hints[] = 'SOP, SK Direksi, Pedoman Kerja, atau Dokumen Kebijakan Resmi';
        }
        if (str_contains($desc, 'catat') || str_contains($desc, 'data') || str_contains($desc, 'informasi') || str_contains($desc, 'identifikasi')) {
            $hints[] = 'Log Data, Risk Register, Dokumen Klasifikasi, Form Isian, atau Rekaman Database';
        }
        if (str_contains($desc, 'pantau') || str_contains($desc, 'evaluasi') || str_contains($desc, 'lapor') || str_contains($desc, 'tinjau') || str_contains($desc, 'monitor')) {
            $hints[] = 'Laporan Audit, Dashboard Monitoring, Notulensi Rapat, atau Laporan Kinerja Berkala';
        }
        if (str_contains($desc, 'laksana') || str_contains($desc, 'terap') || str_contains($desc, 'uji') || str_contains($desc, 'operasi')) {
            $hints[] = 'Screenshot Sistem/Aplikasi, Bukti Testing, Tiket Helpdesk, atau Log Aktivitas Harian';
        }
       
        if (empty($hints)) {
            return 'Dokumen, Catatan, Screenshot Aplikasi, atau Bukti Fisik yang relevan dengan aktivitas ini';
        }

    
        return implode(' ATAU ', $hints);
    }
}