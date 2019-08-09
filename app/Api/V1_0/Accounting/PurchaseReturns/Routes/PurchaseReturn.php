<?php
namespace ERP\Api\V1_0\Accounting\PurchaseReturns\Routes;

use ERP\Api\V1_0\Accounting\PurchaseReturns\Controllers\PurchaseReturnController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class PurchaseReturn implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */

    public function register(RegistrarInterface $Registrar)
    {
		ini_set('memory_limit', '256M');
		// get data
		// insert data post request
		Route::post('Accounting/PurchaseReturns/PurchaseReturn/{purchaseId}','Accounting\PurchaseReturns\Controllers\PurchaseReturnController@store');
	}
}
