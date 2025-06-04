<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('email', 100);
            $table->string('token', 255);
            $table->dateTime('expires_at');
            $table->boolean('used')->default(false);

            $table->foreign('email')
                  ->references('email')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
};
