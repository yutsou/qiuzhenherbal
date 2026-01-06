<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->index('user_id');
            $table->string('merchant_id')->nullable();
            $table->string('merchant_trade_no')->nullable();
            $table->string('l_merchant_trade_no')->nullable();
            $table->string('all_pay_logistics_id')->nullable();
            $table->string('logistics_type');
            $table->string('logistics_sub_type')->nullable();
            $table->string('cvs_store_id')->nullable();
            $table->string('cvs_store_name')->nullable();
            $table->string('cvs_address')->nullable();
            $table->string('cvs_payment_no')->nullable();
            $table->string('cvs_validation_no')->nullable();
            $table->decimal('subtotal');
            $table->decimal('delivery_fee');
            $table->decimal('invite_discount')->nullable();
            $table->decimal('coupon_discount')->nullable();
            $table->integer('point_discount')->nullable();
            $table->decimal('total');
            $table->string('invite_code')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
            $table->string('coupon_name')->nullable();
            $table->string('receiver_name');
            $table->string('receiver_cell_phone');
            $table->string('receiver_email')->nullable();
            $table->tinyInteger('payment_status');
            $table->tinyInteger('delivery_status');
            $table->string('rtn_code')->nullable();
            $table->string('rtn_msg')->nullable();
            $table->string('trade_no')->nullable();
            $table->string('line_pay_transactionId')->nullable();
            $table->string('payment_method');
            $table->string('county')->nullable();
            $table->string('district')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('address')->nullable();
            $table->string('tracking_code')->nullable();
            $table->text('remark')->nullable();
            $table->string('refund_remark')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
