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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Full Name
            $table->string('company_name');
            $table->string('contact_no'); // Mobile No
            $table->text('location');
            $table->string('email'); // Email
            $table->integer('product_id');
            $table->text('logo_design'); // Message
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
};
