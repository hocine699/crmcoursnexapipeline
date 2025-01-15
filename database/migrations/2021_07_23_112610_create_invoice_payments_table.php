<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id')->default(0);
            $table->unsignedBigInteger('client_id')->default(0);
            $table->unsignedBigInteger('invoice_id')->default(0);
            $table->decimal('amount',30,2)->default('0.00');
            $table->date('date');
            $table->unsignedBigInteger('payment_id')->default(0);
            $table->string('payment_type',100)->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt')->nullable();
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
        Schema::dropIfExists('invoice_payments');
    }
}
