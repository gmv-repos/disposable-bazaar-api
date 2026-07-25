<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_details', function (Blueprint $table) {
            $table->boolean('is_customize')->default(false); // Add is_customize column
            $table->unsignedBigInteger('product_option_id')->nullable(); // Add product_option_id column

            // Add foreign key constraint for product_option_id
            $table->foreign('product_option_id')->references('id')->on('product_options')->onDelete('set null');

            $table->string('customize_logo_image')->nullable(); // Add customize_logo_image column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['product_option_id']); // Drop foreign key constraint
            $table->dropColumn(['is_customize', 'product_option_id', 'customize_logo_image']); // Drop the columns
        });
    }
};
