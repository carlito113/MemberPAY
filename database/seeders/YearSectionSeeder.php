<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class YearSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define your programs and their section patterns
        $programSections = [
            'DIGITS' => [
                1 => ['AI11', 'AI12', 'AI13', 'AI14', 'AI15', 'AI16', 'AI17'],
                2 => ['AI21', 'AI22', 'AI23', 'AI24', 'AI25'],
                3 => ['AI31', 'AI32', 'AI33', 'AI34', 'AI35'],
                4 => ['AI41', 'AI42', 'AI43', 'AI44', 'AI45'],
            ],
            'APSS' => [
                1 => ['AP11', 'AP12', 'AP13', 'AP14', 'AP15', 'AP16', 'AP17'],
                2 => ['AP21', 'AP22', 'AP23', 'AP24', 'AP25'],
                3 => ['AP31', 'AP32', 'AP33', 'AP34', 'AP35'],
                4 => ['AP41', 'AP42', 'AP43', 'AP44', 'AP45'],
            ],
            // Add more programs here if needed
        ];

        foreach ($programSections as $programName => $years) {
            // First find the admin by username
            $admin = DB::table('admins')->where('username', $programName)->first();
        
            if ($admin) { // Proceed only if admin exists
                foreach ($years as $year => $sections) {
                    foreach ($sections as $section) {
                        DB::table('yearsections')->insert([
                            'admin_id' => $admin->id, // <-- now using admin_id
                            'year' => $year,
                            'section' => $section,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
        
    }
}
