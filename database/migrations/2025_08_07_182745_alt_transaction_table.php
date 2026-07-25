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
        Schema::table('transaction', function (Blueprint $table) {
            $table
                ->integer('transaction_type')
                ->comment(
                    '1 = Sell, 2 = Receipt, 3 = Purchase, 4 = Payment, 5 = Expense, 6 = Rider Payment, 21 = In to Account, 22 = Out from Account, 23 = Extra Trx In, 24 = Extra Trx Out',
                )
                ->change();
            $table->decimal('amount_in', 15, 2)->nullable()->after('rider_payment_amount');
            $table->decimal('amount_out', 15, 2)->nullable()->after('amount_in');
            $table->decimal('extra_trx_amount', 15, 2)->nullable()->after('amount_out');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction', function (Blueprint $table) {
            //
        });
    }
};
