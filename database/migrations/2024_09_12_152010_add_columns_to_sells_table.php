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
        Schema::table('sells', function (Blueprint $table) {
            // Add new columns
            $table->string('return_remarks')->nullable()->after('deleted_by');
            $table->date('return_date')->nullable()->after('return_remarks');

            // Modify `order_status` column to include a new comment
            $table
                ->tinyInteger('order_status')
                ->default(0)
                ->comment(
                    '0=pending, 1=processing, 2=on_the_way, 3=cancel_request, 4=cancel_accepted, 5=cancel_order_process_completed, 6=order completed, 7=Return Invoice',
                )
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sells', function (Blueprint $table) {
            // Drop newly added columns
            $table->dropColumn('return_remarks');
            $table->dropColumn('return_date');

            // Revert `order_status` column comment to its previous state
            $table
                ->tinyInteger('order_status')
                ->default(0)
                ->comment(
                    '0=pending, 1=processing, 2=on_the_way, 3=cancel_request, 4=cancel_accepted, 5=cancel_order_process_completed, 6=order completed',
                )
                ->change();
        });
    }
};
