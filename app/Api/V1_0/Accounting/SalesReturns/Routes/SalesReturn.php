<?php
namespace ERP\Api\V1_0\Accounting\SalesReturns\Routes;

use ERP\Api\V1_0\Accounting\SalesReturns\Controllers\SalesReturnController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class SalesReturn implements RouteRegistrarInterface
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
		Route::post('Accounting/SalesReturns/SalesReturn/{saleId}','Accounting\SalesReturns\Controllers\SalesReturnController@store');
	}
}


