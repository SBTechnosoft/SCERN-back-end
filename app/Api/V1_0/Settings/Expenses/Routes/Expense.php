<?php
namespace ERP\Api\V1_0\Settings\Expenses\Routes;

use ERP\Api\V1_0\Settings\Expenses\Controllers\ExpenseController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Expense implements RouteRegistrarInterface
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
			Route::get('Settings/Expenses/Expense/{ExpenseId?}', 'Settings\Expenses\Controllers\ExpenseController@getData');
		});
		
		// insert data post request
		Route::post('Settings/Expenses/Expense', 'Settings\Expenses\Controllers\ExpenseController@store');
		
		// update data post request
		Route::post('Settings/Expenses/Expense/{ExpenseId}', 'Settings\Expenses\Controllers\ExpenseController@update');
		
		//delete data delete request
		Route::delete('Settings/Expenses/Expense/{ExpenseId}', 'Settings\Expenses\Controllers\ExpenseController@destroy');
	}
}


