<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::insert([
            [
                'id'             => 1,
                'system_name'    => 'Laravel',
                'email'          => 'support@gmail.com',
                'copyright_text' => 'Copyright © 2017 - 2024 DESIGN AND DEVELOPED BY ❤️',
                'logo'           => 'backend/assets/images/logo.png',
                'favicon'        => 'backend/assets/images/logo.png',
                'platform_fee'   => 5.00,
                'created_at'     => Carbon::now(),
            ],
        ]);
    }
}
