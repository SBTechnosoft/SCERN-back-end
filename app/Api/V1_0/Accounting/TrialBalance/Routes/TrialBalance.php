<?php
namespace ERP\Api\V1_0\Accounting\TrialBalance\Routes;

use ERP\Api\V1_0\Accounting\TrialBalance\Controllers\TrialBalanceController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TrialBalance implements RouteRegistrarInterface
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
			Route::get('Accounting/TrialBalance/TrialBalance/company/{companyId}', 'Accounting\TrialBalance\Controllers\TrialBalanceController@getTrialBalanceData');
			Route::get('Accounting/TrialBalance/TrialBalance/company/{companyId}/export', 'Accounting\TrialBalance\Controllers\TrialBalanceController@getDocumentpath');
		});
		
	}
}


