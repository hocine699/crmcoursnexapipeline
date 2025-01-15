<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->default(0);
            $table->integer('salesorder_id')->default(0);
            $table->string('item')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price',30,2)->default('0.00');
            $table->float('discount')->default(0.00);
            $table->string('tax')->nullable();
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
        Schema::dropIfExists('sales_order_items');
    }
}
