<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id', 50)->primary();
            $table->string('partner_id', 50);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url', 255)->nullable();
            $table->string('status', 20)->default('active');
            $table->enum('category', ['product', 'service']);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('partner_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        // Add foreign key to order_items table now that products table exists
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('products');
    }
};
