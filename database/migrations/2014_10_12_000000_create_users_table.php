<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();;
            $table->string('image')->nullable();
            $table->string('location')->nullable();
            $table->string('Rc')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->required();
            $table->string('password')->required();
            $table->string('role')->default('client'); // Assuming 'user' is the default role
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('email_verified_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
