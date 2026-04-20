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
                    CobitQuestion::updateOrCreate(
                        [
                            'domain_id' => $domain->id,
                            'activity_code' => $currentActivityCode,
                            'description' => $colD, 
                        ],
                        [
                            'capability_level' => $currentLevel,
                            'activity_name' => $currentActivityName, 
                        ]
                    );
                    $count++;
                }
            }
            fclose($file);
            
            $this->command->info("Berhasil import {$count} pertanyaan untuk domain {$code}!");
        }
    }
}