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
        Schema::table('categories_sort_orders', function (Blueprint $table) {
            $table->unique(['category_id', 'section_name']);

            $table->boolean('is_visible')->default(true)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories_sort_orders', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
};
