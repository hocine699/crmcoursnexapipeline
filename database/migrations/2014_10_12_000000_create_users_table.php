<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('name');
            $table->string('title')->nullable();
            $table->integer('plan_is_active')->default(1);
            $table->string('email');
            $table->string('email_verified_at')->nullable();
            $table->float('storage_limit')->default(0);
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('type','20')->nullable();
            $table->integer('is_active')->default(1);
            $table->string('user_roles')->nullable();
            $table->string('lang')->default('en');
            $table->string('password');
            $table->string('mode')->default('light');
            $table->string('avatar')->nullable();
            $table->integer('plan')->nullable();
            $table->date('plan_expire_date')->nullable();
            $table->integer('requested_plan')->default(0);
            $table->integer('created_by')->default(0);
            $table->boolean('active_status')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
