<?php
namespace ERP\Api\V1_0\Accounting\ProfitLoss\Routes;

use ERP\Api\V1_0\Accounting\ProfitLoss\Controllers\ProfitLossController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProfitLoss implements RouteRegistrarInterface
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
			Route::get('Accounting/ProfitLoss/ProfitLoss/company/{companyId}', 'Accounting\ProfitLoss\Controllers\ProfitLossController@getProfitLossData');
			Route::get('Accounting/ProfitLoss/ProfitLoss/company/{companyId}/export', 'Accounting\ProfitLoss\Controllers\ProfitLossController@getDocumentpath');
		});
		
	}
}


