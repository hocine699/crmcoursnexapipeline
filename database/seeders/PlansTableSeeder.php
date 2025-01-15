<?php
namespace Database\Seeders;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::create(
            [
                'name' => 'Free Plan',
                'price' => 0,
                'duration' => 'lifetime',
                'storage_limit' => 1024,
                'max_user' => 5,
                'max_account' => 5,
                'max_contact' => 5,
                'image' => 'free_plan.png',
                'enable_chatgpt' => 'on',
            ]
        );
    }
}
