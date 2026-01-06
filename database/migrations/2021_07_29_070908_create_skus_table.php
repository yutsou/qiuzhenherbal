<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
            $table->string('number')->nullable();
            $table->unique('number');
            $table->decimal('regular_price')->nullable();
            $table->integer('per_order_limit_quantity')->nullable();
            $table->boolean('visible')->default(1);
            $table->integer('inventory')->nullable();
            $table->smallInteger('inventory_status')->nullable();
            $table->string('image_url')->nullable();
            $table->string('alt')->nullable();
            $table->timestamp('discount_start_at')->nullable();
            $table->timestamp('discount_end_at')->nullable();
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
        Schema::dropIfExists('skus');
    }
}
