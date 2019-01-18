<?php
namespace ERP\Api\V1_0\Banks\Routes;

use ERP\Api\V1_0\Banks\Controllers\BankController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Bank implements RouteRegistrarInterface
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
			Route::get('Banks/Bank/branches/{bankId?}', 'Banks\Controllers\BankController@getBranchData');
			Route::get('Banks/Bank/{bankId?}', 'Banks\Controllers\BankController@getData');
		});
	}
}


