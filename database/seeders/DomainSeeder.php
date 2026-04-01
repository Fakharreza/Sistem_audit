<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Domain;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domains = [
            ['code' => 'APO12', 'name' => 'Managed Risk'],
            ['code' => 'APO13', 'name' => 'Managed Security'],
            ['code' => 'BAI06', 'name' => 'Managed IT Changes'],
            ['code' => 'DSS01', 'name' => 'Managed Operations'],
            ['code' => 'MEA01', 'name' => 'Managed Performance and Conformance Monitoring'],
        ];

        foreach ($domains as $domain) {
            Domain::create($domain);
        }
    }
}
