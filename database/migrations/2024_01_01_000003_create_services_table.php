<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->string('service_id', 20)->primary();
            $table->string('partner_id', 20);
            $table->string('title', 255);
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'banned'])->default('pending');
            $table->string('category', 50)->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->timestamps();

            $table->foreign('partner_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
