<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // List of organizations
        $organizations = [
            'APSS', 'AVED', 'BACOMMUNITY', 'BPED MOVERS', 'COFED', 'DIGITS',
            'EC', 'EA', 'HRC', 'JSWAP', 'KMF', 'LNU MSS', 'INTERSOC',
            'TC', 'TLEG', 'SQU', 'ECEO', 'FCO', 'SCO', 'JCO', 'SENCO'
        ];

        // List of sample names
        $names = [
            'John Denver Candasua', 'Jane Doe', 'Michael Smith', 'Emily Johnson',
            'Chris Evans', 'Sarah Connor', 'David Brown', 'Sophia Martinez',
            'James Wilson', 'Olivia Garcia', 'Daniel Lee', 'Emma Davis',
            'Liam Harris', 'Mia Clark', 'Noah Lewis', 'Isabella Walker',
            'Lucas Hall', 'Ava Allen', 'Ethan Young', 'Charlotte King', 'Mason Wright'
        ];

        // Seed admins
        foreach ($organizations as $index => $org) {
            $password = strtolower($org) . '2025'; // e.g., digits2025
        
            // Slugify the organization name for image file naming
            $imageFileName = strtoupper(str_replace(' ', '_', $org)) . '.png'; // e.g., bacommunity.png
        
            Admin::firstOrCreate(
                ['username' => $org],
                [
                    'password' => Hash::make($password),
                    'plain_password' => $password,
                    'role' => 'admin',
                    'name' => $names[$index % count($names)],
                    'image' => 'images/OrganizationLogo/' . $imageFileName,
                ]
            );
        }
        

        // Superadmin
        Admin::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'password' => Hash::make('superadmin123'),
                'plain_password' => 'superadmin123',
                'role' => 'super_admin',
                'name' => 'SUPERADMIN',
            ]
        );

        // Define programs and their section patterns
        $programSections = [
                  
            
            'APSS' => [
                1 => ['AP11', 'AP12', 'AP13', 'AP14', 'AP15', 'AP16', 'AP17'],
                2 => ['AP21', 'AP22', 'AP23', 'AP24', 'AP25'],
                3 => ['AP31', 'AP32', 'AP33', 'AP34', 'AP35'],
                4 => ['AP41', 'AP42', 'AP43'],
            ],
            'AVED' => [
                1 => ['AV11', 'AV12', 'AV13', 'AV14', 'AV15', 'AV16', 'AV17'],
                2 => ['AV21', 'AV22', 'AV23', 'AV24', 'AV25'],
                3 => ['AV31', 'AV32', 'AV33', 'AV34', 'AV35'],
                4 => ['AV41', 'AV42', 'AV43'],
            ],
            'BACOMMUNITY' => [
                1 => ['BA11', 'BA12', 'BA13', 'BA14', 'BA15', 'BA16', 'BA17'],
                2 => ['BA21', 'BA22', 'BA23', 'BA24', 'BA25'],
                3 => ['BA31', 'BA32', 'BA33', 'BA34', 'BA35'],
                4 => ['BA41', 'BA42', 'BA43'],
            ],
            'BPED MOVERS' => [
                1 => ['BP11', 'BP12', 'BP13', 'BP14', 'BP15', 'BP16', 'BP17'],
                2 => ['BP21', 'BP22', 'BP23', 'BP24', 'BP25'],
                3 => ['BP31', 'BP32', 'BP33', 'BP34', 'BP35'],
                4 => ['BP41', 'BP42', 'BP43'],
            ],
            'COFED' => [
                1 => ['CO11', 'CO12', 'CO13', 'CO14', 'CO15', 'CO16', 'CO17'],
                2 => ['CO21', 'CO22', 'CO23', 'CO24', 'CO25'],
                3 => ['CO31', 'CO32', 'CO33', 'CO34', 'CO35'],
                4 => ['CO41', 'CO42', 'CO43'],
            ],
            'DIGITS' => [
                1 => ['AI11', 'AI12', 'AI13', 'AI14', 'AI15', 'AI16', 'AI17'],
                2 => ['AI21', 'AI22', 'AI23', 'AI24', 'AI25'],
                3 => ['AI31', 'AI32', 'AI33', 'AI34', 'AI35'],
                4 => ['AI41', 'AI42', 'AI43', 'AI44', 'AI45'],
            ],
            'EC' => [
                1 => ['EN11', 'EN12', 'EN13', 'EN14', 'EN15', 'EN16', 'EN17'],
                2 => ['EN21', 'EN22', 'EN23', 'EN24', 'EN25'],
                3 => ['EN31', 'EN32', 'EN33', 'EN34', 'EN35'],
                4 => ['EN41', 'EN42', 'EN43'],
            ],
            'EA' => [
                1 => ['EA11', 'EA12', 'EA13', 'EA14', 'EA15', 'EA16', 'EA17'],
                2 => ['EA21', 'EA22', 'EA23', 'EA24', 'EA25'],
                3 => ['EA31', 'EA32', 'EA33', 'EA34', 'EA35'],
                4 => ['EA41', 'EA42', 'EA43'],
            ],
            'HRC' => [
                1 => ['HR11', 'HR12', 'HR13', 'HR14', 'HR15', 'HR16', 'HR17'],
                2 => ['HR21', 'HR22', 'HR23', 'HR24', 'HR25'],
                3 => ['HR31', 'HR32', 'HR33', 'HR34', 'HR35'],
                4 => ['HR41', 'HR42', 'HR43'],
            ],
            'JSWAP' => [
                1 => ['JS11', 'JS12', 'JS13', 'JS14', 'JS15', 'JS16', 'JS17'],
                2 => ['JS21', 'JS22', 'JS23', 'JS24', 'JS25'],
                3 => ['JS31', 'JS32', 'JS33', 'JS34', 'JS35'],
                4 => ['JS41', 'JS42', 'JS43'],
            ],
            'KMF' => [
                1 => ['KM11', 'KM12', 'KM13', 'KM14', 'KM15', 'KM16', 'KM17'],
                2 => ['KM21', 'KM22', 'KM23', 'KM24', 'KM25'],
                3 => ['KM31', 'KM32', 'KM33', 'KM34', 'KM35'],
                4 => ['KM41', 'KM42', 'KM43'],
            ],
            'LNU MSS' => [
                1 => ['LN11', 'LN12', 'LN13', 'LN14', 'LN15', 'LN16', 'LN17'],
                2 => ['LN21', 'LN22', 'LN23', 'LN24', 'LN25'],
                3 => ['LN31', 'LN32', 'LN33', 'LN34', 'LN35'],
                4 => ['LN41', 'LN42', 'LN43'],
            ],
            'INTERSOC' => [
                1 => ['IN11', 'IN12', 'IN13', 'IN14', 'IN15', 'IN16', 'IN17'],
                2 => ['IN21', 'IN22', 'IN23', 'IN24', 'IN25'],
                3 => ['IN31', 'IN32', 'IN33', 'IN34', 'IN35'],
                4 => ['IN41', 'IN42', 'IN43'],
            ],
            'TC' => [
                1 => ['TC11', 'TC12', 'TC13', 'TC14', 'TC15', 'TC16', 'TC17'],
                2 => ['TC21', 'TC22', 'TC23', 'TC24', 'TC25'],
                3 => ['TC31', 'TC32', 'TC33', 'TC34', 'TC35'],
                4 => ['TC41', 'TC42', 'TC43'],
            ],
            'TLEG' => [
                1 => ['TL11', 'TL12', 'TL13', 'TL14', 'TL15', 'TL16', 'TL17'],
                2 => ['TL21', 'TL22', 'TL23', 'TL24', 'TL25'],
                3 => ['TL31', 'TL32', 'TL33', 'TL34', 'TL35'],
                4 => ['TL41', 'TL42', 'TL43'],
            ],
            'SQU' => [
                1 => ['SQ11', 'SQ12', 'SQ13', 'SQ14', 'SQ15', 'SQ16', 'SQ17'],
                2 => ['SQ21', 'SQ22', 'SQ23', 'SQ24', 'SQ25'],
                3 => ['SQ31', 'SQ32', 'SQ33', 'SQ34', 'SQ35'],
                4 => ['SQ41', 'SQ42', 'SQ43'],
            ],
            'ECEO' => [
                1 => ['EC11', 'EC12', 'EC13', 'EC14', 'EC15', 'EC16', 'EC17'],
                2 => ['EC21', 'EC22', 'EC23', 'EC24', 'EC25'],
                3 => ['EC31', 'EC32', 'EC33', 'EC34', 'EC35'],
                4 => ['EC41', 'EC42', 'EC43'],
            ],
        
        ];

        // Seed yearsections
        foreach ($programSections as $programName => $years) {
            // Find admin by username
            $admin = Admin::where('username', $programName)->first();

            if ($admin) {
                foreach ($years as $year => $sections) {
                    foreach ($sections as $section) {
                        DB::table('yearsections')->insert([
                            'admin_id' => $admin->id,
                            'year' => $year,
                            'section' => $section,
                        ]);
                    }
                }
            }
        }
    }
}
