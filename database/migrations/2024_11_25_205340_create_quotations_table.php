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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('status')->default('Pending');
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('delivery_charges', 10, 2);
            $table->decimal('payable_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamp('valid_until');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('pos_customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
};
