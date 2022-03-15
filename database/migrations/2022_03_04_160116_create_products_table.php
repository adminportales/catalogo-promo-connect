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
            $table->string('internal_sku')->nullable()->unique();
            $table->string('sku_parent')->nullable();
            $table->string('sku')->unique();
            $table->string('name')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->integer('stock')->nullable();
            $table->string('type')->nullable();
            $table->string('color')->nullable();
            $table->string('image')->nullable();
            $table->boolean('offer')->nullable();
            $table->integer('discount')->nullable();
            $table->boolean('ecommerce')->nullable();
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
