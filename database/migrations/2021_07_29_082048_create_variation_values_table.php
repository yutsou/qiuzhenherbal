<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variation_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sku_id');
            $table->foreign('sku_id')->references('id')->on('skus')->onDelete('cascade');
            $table->index('sku_id');
            $table->unsignedBigInteger('variation_id');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->index('variation_id');
            $table->string('value');
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
        Schema::dropIfExists('variation_values');
    }
}
