<?php
namespace ERP\Api\V1_0\Accounting\CashFlow\Routes;

use ERP\Api\V1_0\Accounting\CashFlow\Controllers\CashFlowController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CashFlow implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		// all the possible get request 
		Route::group(['as' => 'get'], function ()
		{
			Route::get('Accounting/CashFlow/CashFlow/company/{companyId}', 'Accounting\CashFlow\Controllers\CashFlowController@getCashFlowData');
			Route::get('Accounting/CashFlow/CashFlow/company/{companyId}/export', 'Accounting\CashFlow\Controllers\CashFlowController@getDocumentpath');
		});
		
	}
}


