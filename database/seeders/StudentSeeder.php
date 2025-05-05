<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            'APSS', 'AVED', 'BACOMMUNITY', 'BPED MOVERS', 'COFED', 'DIGITS',
            'EC', 'EA', 'HRC', 'JSWAP', 'KMF', 'LNU MSS', 'INTERSOC',
            'TC', 'TLEG', 'SQU', 'ECEO', 'FCO', 'SCO', 'JCO', 'SENCO'
        ];

        // More diverse names: singers, anime characters, cartoon characters
        $firstNames = [
            'Naruto', 'Sasuke', 'Hinata', 'Mikasa', 'Eren', 'Goku', 'Vegeta', 'Ash', 'Pikachu', 'SpongeBob',
            'Patrick', 'Aang', 'Zuko', 'Korra', 'Elsa', 'Anna', 'Rapunzel', 'Ariel', 'Moana', 'Simba',
            'Ariana', 'Drake', 'Taylor', 'Beyoncé', 'Bruno', 'Billie', 'Harry', 'Olivia', 'The Weeknd', 'SZA'
        ];

        $lastNames = [
            'Uzumaki', 'Uchiha', 'Hyuga', 'Ackerman', 'Yeager', 'Son', 'Briefs', 'Ketchum', 'SquarePants', 'Star',
            'Beifong', 'Agni', 'Water', 'Ice', 'Fire', 'Light', 'Dark', 'Storm', 'Sea', 'Lion',
            'Grande', 'Graham', 'Swift', 'Knowles', 'Mars', 'Eilish', 'Styles', 'Rodrigo', 'Tesfaye', 'Rowe'
        ];

        $idNumber = 2201431;

        foreach ($organizations as $org) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $org), 0, 2)); // First 2 letters only
            $sections = [];
            for ($j = 1; $j <= 5; $j++) {
                $sections[] = $prefix . "1$j"; // e.g., AP11 to AP15
                $sections[] = $prefix . "2$j"; // e.g., AP21 to AP25
            }

            for ($i = 0; $i < 100; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName  = $lastNames[array_rand($lastNames)];
                $section   = $sections[array_rand($sections)];
                $yearLevel = substr($section, 2, 1);

                Student::create([
                    'id_number'    => $idNumber++,
                    'first_name'   => $firstName,
                    'last_name'    => $lastName,
                    'year_level'   => $yearLevel,
                    'section'      => $section,
                    'organization' => $org,
                ]);
            }
        }
    }
}
