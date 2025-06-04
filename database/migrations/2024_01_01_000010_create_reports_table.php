<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->string('reported_by', 20);
            $table->string('reported_user', 20);
            $table->text('reason');
            $table->enum('status', ['pending', 'in review', 'resolved'])->default('pending');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('reported_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('reported_user')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
