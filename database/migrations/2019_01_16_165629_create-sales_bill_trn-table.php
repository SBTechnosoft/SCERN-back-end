<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesBillTrnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sales_bill_trn')) {
            Schema::create('sales_bill_trn', function (Blueprint $table) {
                $table->increments('sale_trn_id');
                $table->char('product_array',3000)->nullable()->collate('utf16_general_ci');
                $table->enum('payment_mode',['cash','bank','card','credit','neft','rtgs','imps','nach','ach'])->nullable()->default('cash')->collate('utf16_general_ci');
                $table->char('bank_name',60)->default('cash')->nullable()->collate('utf16_general_ci');
                $table->integer('bank_ledger_id')->default(0);
                $table->char('invoice_number',50)->nullable()->collate('utf16_general_ci');
                $table->char('job_card_number',50)->nullable()->collate('utf16_general_ci');
                $table->char('check_number',20)->nullable()->collate('utf16_general_ci');
                $table->decimal('total',20,4)->nullable()->default('0.0000');
                $table->enum('total_discounttype',['flat', 'percentage'])->default('flat')->collate('utf16_general_ci');
                $table->decimal('total_discount',20,4)->nullable();
                $table->decimal('total_cgst_percentage',20,4)->nullable()->default('0.0000');
                $table->decimal('total_sgst_percentage',20,4)->nullable()->default('0.0000');
                $table->decimal('total_igst_percentage',20,4)->nullable()->default('0.0000');
                $table->decimal('extra_charge',20,4)->nullable();
                $table->decimal('tax',20,4)->nullable()->default('0.0000');
                $table->decimal('grand_total',20,4)->nullable()->default('0.0000');
                $table->decimal('advance',20,4)->nullable()->default('0.0000');
                $table->decimal('balance',20,4)->nullable()->default('0.0000');
                $table->enum('sales_type',['retail_sales', 'whole_sales'])->nullable()->collate('utf16_general_ci');
                $table->enum('payment_trn',['payment', 'refund', ''])->default('')->collate('utf16_general_ci');
                $table->decimal('refund',20,4)->default('0.0000');
                $table->char('po_number',200)->nullable()->collate('utf16_general_ci');
                $table->char('remark',100)->nullable()->collate('utf16_general_ci');
                $table->date('entry_date')->default('0000-00-00');
                $table->enum('is_salesorder',['ok', 'not'])->default('not')->collate('utf16_general_ci');
                $table->date('service_date')->default('0000-00-00');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->default('0000-00-00 00:00:00');
                $table->dateTime('deleted_at')->default('0000-00-00 00:00:00');
                $table->integer('client_id');
                $table->integer('company_id');
                $table->integer('branch_id')->nullable()->default('0');
                $table->integer('sale_id');
                $table->integer('user_id')->nullable();
                $table->integer('jf_id');

                $table->index('bank_ledger_id');
                $table->index('po_number');
                $table->index('client_id');
                $table->index('company_id');
                $table->index('branch_id');
                $table->index('sale_id');
                $table->index('user_id');
                $table->index('jf_id');
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
        Schema::drop('sales_bill_trn');
    }
}
