<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name')->nullable();
            $table->integer('status')->default(0);
            $table->integer('stage')->default(0);
            $table->integer('priority')->default(0);
            $table->date('start_date');
            $table->date('due_date');
            $table->string('parent')->nullable();
            $table->integer('parent_id')->default(0);
            $table->integer('account')->default(0);
            $table->string('description')->nullable();
            $table->string('attachment')->nullable();
            $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('tasks');
    }
}
