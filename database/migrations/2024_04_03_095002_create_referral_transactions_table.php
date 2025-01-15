<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('comapny_id')->nullable();
            $table->integer('plan_id')->nullable();
            $table->string('plan_price')->nullable();
            $table->string('commission')->nullable();
            $table->string('amount')->nullable();
            $table->string('referral_code')->nullable();
            $table->integer('ref_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_transactions');
    }
};
