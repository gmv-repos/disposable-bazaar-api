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
        Schema::create('expense_accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->string('account_name');
            $table->integer('status')->default(1)->comment('1 = Active , 2 = Inactive');
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
        Schema::table('expense_accounts', function (Blueprint $table) {
            Schema::dropIfExists('expense_accounts');
        });
    }
};
