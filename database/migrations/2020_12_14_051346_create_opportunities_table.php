<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('campaign')->default(0);
            $table->string('name')->nullable();
            $table->integer('account')->default(0);
            $table->integer('stage')->default(0);
            $table->decimal('amount',30,2)->default('0.00');
            $table->string('probability')->nullable();
            $table->string('close_date')->nullable();
            $table->integer('contact')->default(0);
            $table->string('lead_source')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('opportunities');
    }
}
