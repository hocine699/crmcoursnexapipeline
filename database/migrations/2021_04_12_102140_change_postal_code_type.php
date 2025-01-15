<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePostalCodeType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'accounts', function (Blueprint $table){
            $table->string('billing_postalcode')->nullable()->change();
            $table->string('shipping_postalcode')->nullable()->change();
        }
        );

        Schema::table(
            'contacts', function (Blueprint $table){
            $table->string('contact_postalcode')->nullable()->change();
        }
        );


        Schema::table(
            'leads', function (Blueprint $table){
            $table->string('lead_postalcode')->nullable()->change();
        }
        );


        Schema::table(
            'quotes', function (Blueprint $table){
            $table->string('billing_postalcode')->nullable()->change();
            $table->string('shipping_postalcode')->nullable()->change();
        }
        );


        Schema::table(
            'invoices', function (Blueprint $table){
            $table->string('billing_postalcode')->nullable()->change();
            $table->string('shipping_postalcode')->nullable()->change();
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
        Schema::table(
            'accounts', function (Blueprint $table){
            $table->dropColumn('billing_postalcode');
            $table->dropColumn('shipping_postalcode');
        }
        );

        Schema::table(
            'contacts', function (Blueprint $table){
            $table->dropColumn('contact_postalcode');
        }
        );

        Schema::table(
            'leads', function (Blueprint $table){
            $table->dropColumn('lead_postalcode');
        }
        );

        Schema::table(
            'quotes', function (Blueprint $table){
            $table->dropColumn('billing_postalcode');
            $table->dropColumn('shipping_postalcode');
        }
        );


        Schema::table(
            'invoices', function (Blueprint $table){
            $table->dropColumn('billing_postalcode');
            $table->dropColumn('shipping_postalcode');
        }
        );
    }
}
