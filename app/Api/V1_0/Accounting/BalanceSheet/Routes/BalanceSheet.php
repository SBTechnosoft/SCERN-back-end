<?php
namespace ERP\Api\V1_0\Accounting\BalanceSheet\Routes;

use ERP\Api\V1_0\Accounting\BalanceSheet\Controllers\BalanceSheetController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BalanceSheet implements RouteRegistrarInterface
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
			Route::get('Accounting/BalanceSheet/BalanceSheet/company/{companyId}', 'Accounting\BalanceSheet\Controllers\BalanceSheetController@getBalanceSheetData');
			Route::get('Accounting/BalanceSheet/BalanceSheet/company/{companyId}/export', 'Accounting\BalanceSheet\Controllers\BalanceSheetController@getDocumentpath');
		});
		
	}
}


