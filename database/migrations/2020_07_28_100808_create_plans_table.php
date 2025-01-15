<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
        Schema::create(
            'plans', function (Blueprint $table){
            $table->id();
            $table->string('name', 100)->unique();
            $table->decimal('price',30,2)->default('0.00')->nullable();
            $table->string('duration', 100);
            $table->float('storage_limit')->default(0);
            $table->integer('max_user')->default(0);
            $table->integer('max_account')->default(0);
            $table->integer('max_contact')->default(0);
            $table->text('description')->nullable();
            $table->string('enable_chatgpt')->default('off');
            $table->string('image')->nullable();
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
