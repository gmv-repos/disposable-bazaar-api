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
        Schema::create('receipt_voucher', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('voucher_no');
            $table->date('receipt_date');
            $table->decimal('amount', 15, 3);
            $table->string('description');
            $table->integer('status')->comment('1 = Active, 2 = Inactive');
            $table->date('created_date');
            $table->string('created_by');
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
        Schema::dropIfExists('receipt_voucher');
    }
};
