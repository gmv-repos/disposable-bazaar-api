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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->date('order_date');
            $table->string('order_no')->unique();
            $table->decimal('total_amount', 15, 3);
            $table->decimal('shipping_charges', 15, 3);
            $table->decimal('discount_amount', 15, 3)->nullable();
            $table->decimal('grand_total', 15, 3);
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('dispatch_date')->nullable();
            $table
                ->integer('order_status')
                ->default(1)
                ->comment('1 = Pending  , 2 = Confirmed , 3 = on the way , 4 = Completed, 5 = Declined');
            $table->integer('status')->default(1)->comment('1 = Active, 2 = Inactive');
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
        Schema::dropIfExists('orders');
    }
};
