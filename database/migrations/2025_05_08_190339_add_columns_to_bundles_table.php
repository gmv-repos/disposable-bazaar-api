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
        Schema::table('bundles', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->string('main_image')->nullable()->after('description');
            $table->string('meta_title')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('focus_keyword')->nullable();
            $table->string('redirect_301')->nullable();
            $table->string('redirect_302')->nullable();
            $table->string('schema')->nullable();
        });

        Schema::create('bundle_images', function (Blueprint $table) {
            $table->id();
            $table->string('bundle_id')->nullable();
            $table->string('image')->nullable();
            $table->string('alt')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bundles', function (Blueprint $table) {
            //
        });
    }
};
