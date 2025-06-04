<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('design_previews', function (Blueprint $table) {
            $table->id('preview_id');
            $table->string('design_id', 20);
            $table->string('image_url', 255);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('design_id')
                  ->references('design_id')
                  ->on('designs')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('design_previews');
    }
};
