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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_id');
            $table->integer('transaction_type')->comment('1 = Sell, 2 = Receipt, 3 = Purchase, 4 = Payment');
            $table->string('voucher_no');
            $table->date('transaction_date');
            $table->decimal('gross_amount', 15, 3);
            $table->decimal('discount_amount', 15, 3);
            $table->decimal('payable_amount', 15, 3);
            $table->decimal('receiveable_amount', 15, 3);
            $table->decimal('receipt_amount', 15, 3);
            $table->decimal('paid_amount', 15, 3);
            $table->text('particular');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction');
    }
};
