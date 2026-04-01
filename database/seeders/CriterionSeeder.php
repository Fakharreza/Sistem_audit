<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Criterion;

class CriterionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $criteria = [
            ['name' => 'Urgensi', 'weight' => 0.50, 'type' => 'benefit'],
            
            ['name' => 'Biaya', 'weight' => 0.30, 'type' => 'cost'],
            
            ['name' => 'Kemudahan', 'weight' => 0.20, 'type' => 'benefit'],
        ];

        foreach ($criteria as $criterion) {
            Criterion::create($criterion);
        }
    }
}
