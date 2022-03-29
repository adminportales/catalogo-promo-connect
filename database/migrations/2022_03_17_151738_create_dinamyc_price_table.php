<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDinamycPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dinamyc_price', function (Blueprint $table) {
            $table->id();
            $table->enum("type", ['PORCENTAJE', "MONTO"]);
            $table->enum("provider_change", ['PROVEEDOR', "INTERNO"]);
            $table->enum("type_change", ['DESCONTAR', "SUMAR"]);
            $table->decimal("amount", 8, 2);
            $table->foreignId('product_id')->constrained();
            $table->foreignId('site_id')->nullable()->constrained();
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
        Schema::dropIfExists('dinamyc_price');
    }
}
