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
        Schema::create('sms_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // Reference to the order
            $table->string('phone_number'); // The recipient's phone number
            $table->string('recipient_name'); // The recipient's name (for personalization)

            // Change the status column to integer with a comment
            $table->integer('status')->default(1)->comment('1: pending, 2: forward, 3: error');

            $table->string('message_type'); // Type of message (like order status update)
            $table->text('message'); // The SMS message content
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_details');
    }
};
