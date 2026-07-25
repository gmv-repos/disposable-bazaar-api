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
        Schema::create('blog_images', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing bigint UNSIGNED column named `id`
            $table->string('blog_id'); // VARCHAR(191) for `blog_id`
            $table->string('image'); // VARCHAR(191) for `image`
            $table->tinyInteger('status')->default(1)->comment('active=1, inactive=0'); // TINYINT for `status`
            $table->timestamps(); // Creates `created_at` and `updated_at` TIMESTAMP columns
            $table->bigInteger('created_by')->nullable(); // BIGINT for `created_by`
            $table->bigInteger('updated_by')->nullable(); // BIGINT for `updated_by`
            $table->tinyInteger('deleted')->default(0)->comment('not delete=0, deleted=1'); // TINYINT for `deleted`
            $table->timestamp('deleted_at')->nullable(); // TIMESTAMP for `deleted_at`
            $table->bigInteger('deleted_by')->nullable(); // BIGINT for `deleted_by`
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_images');
    }
};
