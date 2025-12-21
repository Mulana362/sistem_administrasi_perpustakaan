<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['nis'=>'101','name'=>'Ahmad','class'=>'7A'],
            ['nis'=>'102','name'=>'Siti','class'=>'7B'],
            ['nis'=>'103','name'=>'Budi','class'=>'7C'],
            ['nis'=>'104','name'=>'Rina','class'=>'8A'],
            ['nis'=>'105','name'=>'Dedi','class'=>'8B'],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
