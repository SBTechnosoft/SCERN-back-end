<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleDocDtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sale_doc_dtl')) {
            Schema::create('sale_doc_dtl', function (Blueprint $table) {
                $table->increments('document_id');
                $table->char('document_name',35)->nullable()->collate('utf16_general_ci');
                $table->integer('document_size');
                $table->char('document_format',10)->nullable()->collate('utf16_general_ci');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->default('0000-00-00 00:00:00');
                $table->dateTime('deleted_at')->default('0000-00-00 00:00:00');
                $table->integer('jf_id');

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
        Schema::drop('sale_doc_dtl');
    }
}
