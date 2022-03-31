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
            $table->string('internal_sku')->unique();
            $table->string('sku_parent')->nullable();
            $table->string('sku');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->boolean('producto_promocion')->nullable();
            $table->decimal('descuento', 8, 2)->default(0.00);
            $table->boolean('producto_nuevo')->nullable();
            $table->boolean('precio_unico')->nullable();
            $table->boolean('disponible')->nullable();
            $table->foreignId('type_id')->nullable()->constrained();
            $table->foreignId('color_id')->nullable()->constrained();
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
