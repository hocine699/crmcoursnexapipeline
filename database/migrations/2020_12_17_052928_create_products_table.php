<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name')->nullable();
            $table->integer('status')->default(0);
            $table->integer('category')->default(0);
            $table->integer('brand')->default(0);
            $table->decimal('price',30,2)->default('0.00');
            $table->string('tax')->default(0);
            $table->integer('part_number')->nullable();
            $table->string('weight')->nullable();
            $table->string('URL')->nullable();
            $table->string('sku',225)->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('products');
    }
}
