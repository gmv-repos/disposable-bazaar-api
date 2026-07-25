<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusColumnInSmsDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('sms_details', function (Blueprint $table) {
            // Alter the 'status' column to add comments
            //$table->integer('status')->comment('1 = Pending, 2 = Forward, 3 = Server Error, 4 = Invalid Number')->change();
        });
    }

    public function down()
    {
        Schema::table('sms_details', function (Blueprint $table) {
            // Optionally, you can remove the comment in the down method
            //$table->integer('status')->comment('1 = Pending, 2 = Forward')->change();
        });
    }
}
