<?php
namespace ERP\Api\V1_0\Accounting\Quotations\Routes;

use ERP\Api\V1_0\Accounting\Quotations\Controllers\QuotationController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Quotation implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		// get data
		Route::get('Accounting/Quotations/Quotation', 'Accounting\Quotations\Controllers\QuotationController@getSearchingData');
		Route::get('Accounting/Quotations/Quotation/status', 'Accounting\Quotations\Controllers\QuotationController@getStatusData');
		Route::get('Accounting/Quotations/Quotation/status/{companyId}', 'Accounting\Quotations\Controllers\QuotationController@QuotationStatusCounts');
		// insert data post request
		Route::post('Accounting/Quotations/Quotation', 'Accounting\Quotations\Controllers\QuotationController@store');
		// update data post request
		Route::post('Accounting/Quotations/Quotation/{quotationBillId}', 'Accounting\Quotations\Controllers\QuotationController@update');

		Route::post('Accounting/Quotations/Quotation/convert/{quotationBillId}', 'Accounting\Quotations\Controllers\QuotationController@convert');
		// dispatch of items 
		Route::get('Accounting/Quotations/Quotation/dispatch/{saleId}', 'Accounting\Quotations\Controllers\QuotationController@getDispatchData');
		Route::post('Accounting/Quotations/Quotation/dispatch/{saleId}', 'Accounting\Quotations\Controllers\QuotationController@dispatch');

		//delete data
		Route::DELETE('Accounting/Quotations/Quotation/{quotationBillId}/{dataType?}', 'Accounting\Quotations\Controllers\QuotationController@destroySalesOrderData');
	}
}


