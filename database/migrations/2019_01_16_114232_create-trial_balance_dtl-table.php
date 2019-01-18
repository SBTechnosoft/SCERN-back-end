<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrialBalanceDtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('trial_balance_dtl')) {
            Schema::create('trial_balance_dtl', function (Blueprint $table) {
                $table->increments('trial_balance_id');
                $table->decimal('amount',20,4);
                $table->enum('amount_type',['credit', 'debit'])->collate('utf16_general_ci');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->default('0000-00-00 00:00:00');
                $table->dateTime('deleted_at')->default('0000-00-00 00:00:00');
                $table->integer('ledger_id');
                
                $table->index('ledger_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trial_balance_dtl');
    }
}
