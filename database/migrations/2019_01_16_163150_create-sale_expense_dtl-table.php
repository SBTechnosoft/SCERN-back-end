<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleExpenseDtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sale_expense_dtl')) {
            Schema::create('sale_expense_dtl', function (Blueprint $table) {
                $table->increments('sale_expense_id');
                $table->char('expense_name',100)->nullable()->collate('utf16_general_ci');
                $table->enum('expense_operation',['plus', 'minus'])->collate('utf16_general_ci');
                $table->enum('expense_type',['flat', 'percentage'])->collate('utf16_general_ci');
                $table->decimal('expense_value',20,4)->default('0.0000');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->default('0000-00-00 00:00:00');
                $table->dateTime('deleted_at')->default('0000-00-00 00:00:00');
                $table->integer('sale_id');
                $table->integer('expense_id');

                $table->index('sale_id');
                $table->index('expense_id');
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
        Schema::drop('sale_expense_dtl');
    }
}
