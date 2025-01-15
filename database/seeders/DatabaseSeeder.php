<?php

namespace Database\Seeders;

use App\Models\Utility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (!file_exists(storage_path() . "/installed")) {
            $this->call(PlansTableSeeder::class);
            $this->call(UsersTableSeeder::class);
            $this->call(NotificationSeeder::class);
            $this->call(AiTemplateSeeder::class);

        } else {
            Utility::languagecreate();
        }
        Artisan::call('module:migrate LandingPage');
        Artisan::call('module:seed LandingPage');
    }
}
