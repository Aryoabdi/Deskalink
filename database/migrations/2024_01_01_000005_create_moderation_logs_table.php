<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('moderation_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->string('content_id', 20);
            $table->enum('content_type', ['service', 'design']);
            $table->string('moderator_id', 20);
            $table->enum('action', ['approved', 'rejected', 'banned', 'pending']);
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('moderator_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('moderation_logs');
    }
};
