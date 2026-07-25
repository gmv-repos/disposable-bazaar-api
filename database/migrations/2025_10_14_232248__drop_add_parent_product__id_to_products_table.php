<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop 'parent_product_Id' if it exists
            if (Schema::hasColumn('products', 'parent_product_Id')) {
                $table->dropColumn('parent_product_Id');
            }

            // Drop 'parent_product_id' if it exists
            if (Schema::hasColumn('products', 'parent_product_id')) {
                $table->dropColumn('parent_product_id');
            }
        });

        // Add the correct column
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_product_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
