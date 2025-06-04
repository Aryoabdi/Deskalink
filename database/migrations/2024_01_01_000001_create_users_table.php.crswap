<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('user_id', 20)->primary();
            $table->string('google_id', 50)->nullable();
            $table->string('username', 50);
            $table->string('password')->nullable();
            $table->string('full_name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone_number', 20);
            $table->enum('role', ['client', 'partner', 'admin'])->default('client');
            $table->enum('status', ['active', 'suspended', 'banned'])->default('active');
            $table->string('profile_image', 255)->default('https://i.postimg.cc/qqChrG8y/profile.png');
            $table->text('description')->nullable();
            $table->boolean('is_profile_completed')->default(false);
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
