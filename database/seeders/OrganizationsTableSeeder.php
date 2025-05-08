<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now();

        $orgs = [
            'APSS', 'AVED', 'BACOMMUNITY', 'BPED MOVERS', 'COFED', 'DIGITS',
            'EC', 'EA', 'HRC', 'JSWAP', 'KMF', 'LNU MSS', 'INTERSOC',
            'TC', 'TLEG', 'SQU', 'ECEO', 'FCO', 'SCO', 'JCO', 'SENCO'
        ];

        $yearOrgs = ['FCO', 'SCO', 'JCO', 'SENCO'];

        $insertData = collect($orgs)->map(function ($org) use ($yearOrgs, $now) {
            return [
                'name' => $org,
                'code' => $org,
                'type' => in_array($org, $yearOrgs) ? 'year' : 'course',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        DB::table('organizations')->insert($insertData);
    }
}
