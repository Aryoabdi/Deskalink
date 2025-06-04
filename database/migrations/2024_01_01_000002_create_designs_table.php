<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('designs', function (Blueprint $table) {
            $table->string('design_id', 20)->primary();
            $table->string('partner_id', 20);
            $table->string('title', 100);
            $table->text('description');
            $table->integer('price');
            $table->enum('status', ['pending', 'approved', 'rejected', 'banned'])->default('pending');
            $table->string('file_url', 255)->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->string('category', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('partner_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('designs');
    }
};
