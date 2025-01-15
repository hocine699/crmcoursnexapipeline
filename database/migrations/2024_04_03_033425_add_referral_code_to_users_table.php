<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('referral_code')->default(0)->after('plan');
                $table->integer('referral_user')->default(0)->after('referral_code');
                $table->integer('commission_amount')->default(0)->after('referral_user');
            });
        }
        if (Schema::hasColumn('users', 'referral_code')){
            $users = \DB::table('users')->where('type','owner')->get();
            foreach ($users as $user) {
                do {
                    $code = rand(100000, 999999);
                } while (\DB::table('users')->where('referral_code', $code)->exists());
                \DB::table('users')->where('id', $user->id)->update(['referral_code' => $code]);
            }
        }

    }

};
