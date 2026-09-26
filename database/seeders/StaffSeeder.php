<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        Staff::create([
            'fullname' => 'สมหญิง พนักงาน',
            'password' => Hash::make('staff1234'),
            'role' => 'staff',
        ]);

        Staff::create([
            'fullname' => 'สมชาย แอดมิน',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
        ]);
    }
}