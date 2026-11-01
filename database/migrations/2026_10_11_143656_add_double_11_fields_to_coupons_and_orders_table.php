<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDouble11FieldsToCouponsAndOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('min_spend', 10, 2)->default(0)->after('discount_price');
            $table->boolean('is_accumulative')->default(false)->after('min_spend');
            $table->unsignedBigInteger('order_id')->nullable()->after('expired');
            $table->index('order_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('coupon_count')->default(1)->after('coupon_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropColumn(['min_spend', 'is_accumulative', 'order_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['coupon_count']);
        });
    }
}
