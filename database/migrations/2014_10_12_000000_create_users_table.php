<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('confirm_token')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken()->nullable();
            $table->integer('point')->default(0);
            $table->string('phone')->nullable();
            $table->timestamp('birthday')->nullable();
            $table->string('county')->nullable();
            $table->string('district')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('address')->nullable();
            $table->tinyInteger('role');
            $table->string('oauth_type')->nullable();
            $table->string('oauth_id')->nullable();
            $table->string('oauth_email')->nullable();
            $table->unique(['oauth_type', 'oauth_id']);
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
        Schema::dropIfExists('users');
    }
}
