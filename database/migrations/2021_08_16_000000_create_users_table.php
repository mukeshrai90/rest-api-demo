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
            $table->integer('role_id');
            $table->string('name')->nullable();
            $table->string('user_name')->unique();
            $table->string('email')->unique()->nullable();
			$table->string('avatar')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
			$table->integer('verification_code')->nullable();
			$table->integer('verified')->nullable();
            $table->rememberToken();
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
