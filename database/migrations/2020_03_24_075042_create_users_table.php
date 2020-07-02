<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->string('name',50)->nullable();
            $table->string('msisdn',20)->nullable();
            $table->string('password',10)->nullable();
            $table->string('push_token',255)->nullable();
            $table->string('session',255)->nullable();
            $table->string('email',100)->nullable();
            $table->string('avatar',255)->nullable();
            $table->string('address',255)->nullable();
            $table->smallInteger('sex')->nullable();
            $table->date('birthday')->nullable();
            $table->string('platform',100)->nullable();
            $table->string('login_type',25)->nullable();
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
