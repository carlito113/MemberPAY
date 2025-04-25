<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            'DIGITS' => ['AI11', 'AI12', 'AI21', 'AI22', 'AI31', 'AI32', 'AI41', 'AI42'],
            'JCO'    => ['JC11', 'JC12', 'JC21', 'JC22', 'JC31', 'JC32', 'JC41', 'JC42'],
            'SENCO'  => ['SE11', 'SE12', 'SE21', 'SE22', 'SE31', 'SE32', 'SE41', 'SE42'],
            'APSS'   => ['AP11', 'AP12', 'AP21', 'AP22', 'AP31', 'AP32', 'AP41', 'AP42'],
        ];

        $firstNames = ['John', 'Jane', 'Michael', 'Emily', 'Chris', 'Sarah', 'David', 'Sophia'];
        $lastNames  = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];

        $idNumber = 2201431;

        foreach ($organizations as $org => $sections) {
            foreach ($sections as $section) {
                $yearLevel = substr($section, 2, 1); // e.g., AI11 → 1st year

                // 🧑 Add one student per section
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName  = $lastNames[array_rand($lastNames)];

                Student::create([
                    'id_number'    => $idNumber++, // Manual increment
                    'first_name'         => "$firstName",
                    'last_name'          => "$lastName",
                    'year_level'   => $yearLevel,
                    'section'      => $section,
                    'organization' => $org,
                ]);
            }
        }
    }
}
