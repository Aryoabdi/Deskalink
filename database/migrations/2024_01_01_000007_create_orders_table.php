<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_id', 50)->primary();
            $table->string('user_id', 50);
            $table->decimal('total_amount', 10, 2);
            $table->string('status', 20)->default('pending');
            $table->timestamp('order_date')->useCurrent();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 50);
            $table->string('product_id', 50);
            $table->integer('quantity');
            $table->decimal('price', 10, 2);

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
