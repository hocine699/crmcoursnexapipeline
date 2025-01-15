<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'leads', function (Blueprint $table){
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('name')->nullable();
            $table->integer('account')->default(0);
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('title')->nullable();
            $table->string('website')->nullable();
            $table->text('lead_address')->nullable();
            $table->string('lead_city')->nullable();
            $table->string('lead_state')->nullable();
            $table->string('lead_country')->nullable();
            $table->integer('lead_postalcode')->default(0);
            $table->string('status', 20)->nullable();
            $table->string('source')->nullable();
            $table->decimal('opportunity_amount',30,2)->default('0.00');
            $table->integer('campaign')->default(0);
            $table->string('industry')->nullable();
            $table->string('is_converted')->default(0);
            $table->string('description')->nullable();
            $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('leads');
    }
}
