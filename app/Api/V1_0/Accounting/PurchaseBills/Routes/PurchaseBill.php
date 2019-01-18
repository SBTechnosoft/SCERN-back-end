<?php
namespace ERP\Api\V1_0\Accounting\PurchaseBills\Routes;

use ERP\Api\V1_0\Accounting\PurchaseBills\Controllers\PurchaseBillController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PurchaseBill implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		ini_set('memory_limit', '256M');
		// get data
		Route::get('Accounting/PurchaseBills/PurchaseBill/company/{companyId}', 'Accounting\PurchaseBills\Controllers\PurchaseBillController@getData');
		Route::get('Accounting/PurchaseBills/PurchaseBill', 'Accounting\PurchaseBills\Controllers\PurchaseBillController@getPurchaseBillData');
		
		// insert data post request
		Route::post('Accounting/PurchaseBills/PurchaseBill', 'Accounting\PurchaseBills\Controllers\PurchaseBillController@store');
		
		//update data post request
		Route::post('Accounting/PurchaseBills/PurchaseBill/{purchaseBillId}', 'Accounting\PurchaseBills\Controllers\PurchaseBillController@update');
		
		//delete
		Route::delete('Accounting/PurchaseBills/PurchaseBill/{purchaseBillId}', 'Accounting\PurchaseBills\Controllers\PurchaseBillController@destroy');
	}
}


