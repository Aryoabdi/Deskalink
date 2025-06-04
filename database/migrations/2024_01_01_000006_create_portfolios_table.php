<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id('portfolio_id');
            $table->string('partner_id', 20);
            $table->string('title', 100);
            $table->text('description');
            $table->string('image_url', 255)->nullable();
            $table->string('document_url', 255)->nullable();
            $table->enum('type', ['karya', 'sertifikat', 'penghargaan', 'lainnya'])->default('karya');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('partner_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('portfolios');
    }
};
