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
            $table->string('sku_parent')->nullable();
            $table->string('sku');
            $table->string('name')->nullable();
            $table->decimal('price',8,2)->nullable();
            $table->text('description')->nullable();
            $table->string('stock')->nullable();
            $table->string('type')->nullable();
            $table->string('color')->nullable();
            $table->string('image')->nullable();
            $table->boolean('offer')->nullable();
            $table->integer('discount')->nullable();
            $table->foreignId('provider_id')->nullable()->constrained();
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
