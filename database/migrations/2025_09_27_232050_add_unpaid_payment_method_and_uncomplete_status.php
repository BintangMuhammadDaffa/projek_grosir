<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUnpaidPaymentMethodAndUncompleteStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify payment_method enum to include 'unpaid'
        DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_method ENUM('cash', 'transfer', 'credit', 'unpaid') NOT NULL");

        // Modify status enum to include 'uncomplete'
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled', 'uncomplete') DEFAULT 'completed'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
}
