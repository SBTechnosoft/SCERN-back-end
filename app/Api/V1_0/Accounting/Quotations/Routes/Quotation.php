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
		
		// insert data post request
		Route::post('Accounting/Quotations/Quotation', 'Accounting\Quotations\Controllers\QuotationController@store');
		
		// update data post request
		Route::post('Accounting/Quotations/Quotation/{quotationBillId}', 'Accounting\Quotations\Controllers\QuotationController@update');
		
		//delete data
		Route::DELETE('Accounting/Quotations/Quotation/{quotationBillId}', 'Accounting\Quotations\Controllers\QuotationController@destroySalesOrderData');
	}
}


