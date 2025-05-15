<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $idNumberBase = [
            1 => 2401000,
            2 => 2301000,
            3 => 2201000,
            4 => 2101000,
        ];

        $yearCounters = [1 => 0, 2 => 0, 3 => 0, 4 => 0];

        $orgThemes = [
            'APSS' => ['Bruno', 'Drake', 'The Weeknd', 'Justin', 'Harry', 'Charlie'],
            'AVED' => ['Ariana', 'Taylor', 'Billie', 'Olivia', 'SZA', 'Doja'],
            'BACOMMUNITY' => ['Moira', 'Zild', 'December', 'Ben', 'KZ', 'Juan'],
            'BPED MOVERS' => ['SpongeBob', 'Patrick', 'Squidward', 'Sandy', 'Plankton'],
            'COFED' => ['Gumball', 'Darwin', 'Anais', 'Nicole', 'Richard'],
            'DIGITS' => ['IronMan', 'Thor', 'Hulk', 'BlackWidow', 'Hawkeye'],
            'EC' => ['Frodo', 'Sam', 'Gandalf', 'Aragorn', 'Legolas'],
            'EA' => ['Naruto', 'Sasuke', 'Sakura', 'Kakashi'],
            'HRC' => ['Elsa', 'Anna', 'Rapunzel', 'Ariel'],
            'JSWAP' => ['Ash', 'Pikachu', 'Misty', 'Brock'],
            'KMF' => ['Mario', 'Luigi', 'Peach', 'Bowser'],
            'LNU MSS' => ['Sonic', 'Tails', 'Knuckles', 'Amy'],
            'INTERSOC' => ['Rick', 'Morty', 'Summer', 'Beth'],
            'TC' => ['Tom', 'Jerry', 'Spike', 'Tyke'],
            'TLEG' => ['Batman', 'Superman', 'WonderWoman', 'Flash'],
            'SQU' => ['Goku', 'Vegeta', 'Gohan', 'Piccolo'],
            'ECEO' => ['Doraemon', 'Nobita', 'Shizuka', 'Gian'],
        ];

        $lastNames = ['Smith', 'Johnson', 'Lee', 'Garcia', 'Martinez', 'Williams', 'Brown', 'Davis', 'Rodriguez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White'];

        $yearLevelOrgs = [
            1 => 'FCO',
            2 => 'SCO',
            3 => 'JCO',
            4 => 'SENCO',
        ];

        foreach ($orgThemes as $org => $firstNames) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $org), 0, 2));
            $sectionsByYear = [];

            for ($year = 1; $year <= 4; $year++) {
                for ($num = 1; $num <= 5; $num++) {
                    $sectionsByYear[$year][] = $prefix . $year . $num;
                }
            }

            $orgModel = Organization::where('name', $org)->first();

            for ($i = 0; $i < 150; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $yearLevel = rand(1, 4);
                $section = $sectionsByYear[$yearLevel][array_rand($sectionsByYear[$yearLevel])];
                $idNumber = $idNumberBase[$yearLevel] + $yearCounters[$yearLevel]++;

                // Create one student
                $student = Student::create([
                    'id_number'    => $idNumber,
                    'first_name'   => $firstName,
                    'last_name'    => $lastName,
                    'contact_number' => '09' . rand(100000000, 999999999),
                    'year_level'   => $yearLevel,
                    'section'      => $section,
                    'organization' => $org,
                ]);

                // Attach to main org
                if ($orgModel) {
                    $student->organizations()->attach($orgModel->id);
                }

                // Attach to year-level org
                $yearOrgCode = $yearLevelOrgs[$yearLevel] ?? null;
                if ($yearOrgCode) {
                    $yearOrgModel = Organization::where('code', $yearOrgCode)->first();
                    if ($yearOrgModel) {
                        $student->organizations()->attach($yearOrgModel->id);
                    }
                }
            }
        }
    }
}
