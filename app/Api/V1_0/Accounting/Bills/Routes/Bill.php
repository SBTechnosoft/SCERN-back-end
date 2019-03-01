<?php
namespace ERP\Api\V1_0\Accounting\Bills\Routes;

use ERP\Api\V1_0\Accounting\Bills\Controllers\BillController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Bill implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		ini_set('memory_limit', '256M');
		// get data
		Route::get('Accounting/Bills/Bill/company/{companyId}', 'Accounting\Bills\Controllers\BillController@getData');
		Route::get('Accounting/Bills/Bill', 'Accounting\Bills\Controllers\BillController@getPreviosNextData');
		Route::get('Accounting/Bills/Bill/draft-bill/{companyId}', 'Accounting\Bills\Controllers\BillController@getDraftData');
		Route::get('Accounting/Bills/Bill/bulk-print', 'Accounting\Bills\Controllers\BillController@getBulkPrintData');
		Route::get('Accounting/Bills/Bill/{jfId}', 'Accounting\Bills\Controllers\BillController@getBillByJfId');
		// insert data post request
		Route::post('Accounting/Bills/Bill', 'Accounting\Bills\Controllers\BillController@store');
		Route::post('Accounting/Bills/Bill/draft-bill', 'Accounting\Bills\Controllers\BillController@storeDraftData');
		
		//update data post request
		Route::post('Accounting/Bills/Bill/{saleId}','Accounting\Bills\Controllers\BillController@update');
		Route::post('Accounting/Bills/Bill/{saleId}/payment', 'Accounting\Bills\Controllers\BillController@updateBillPayment');
		
		//delete
		Route::delete('Accounting/Bills/Bill/{saleId}', 'Accounting\Bills\Controllers\BillController@destroy');
		Route::delete('Accounting/Bills/Bill/draft-bill/{saleId}', 'Accounting\Bills\Controllers\BillController@destroyDraftData');
		Route::delete('Accounting/Bills/Bill/sales-order/{saleId}', 'Accounting\Bills\Controllers\BillController@destroySalesOrderData');
	}
}


