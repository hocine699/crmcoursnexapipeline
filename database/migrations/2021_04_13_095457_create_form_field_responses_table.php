<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormFieldResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'form_field_responses', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('form_id')->default(0);
            $table->integer('name_id')->default(0);
            $table->integer('email_id')->default(0);
            $table->integer('phone_id')->default(0);
            $table->integer('address_id')->default(0);
            $table->integer('city_id')->default(0);
            $table->integer('state_id')->default(0);
            $table->integer('country_id')->default(0);
            $table->integer('postal_code')->default(0);
            $table->integer('description_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->string('type')->nullable();
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
        Schema::dropIfExists('form_field_responses');
    }
}
