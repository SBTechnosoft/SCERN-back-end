<?php
namespace ERP\Api\V1_0\States\Routes;

use ERP\Api\V1_0\States\Controllers\StateController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class State implements RouteRegistrarInterface
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
			Route::get('States/State/{stateId?}', 'States\Controllers\StateController@getData');
		});
		// insert data post request
		Route::post('States/State', 'States\Controllers\StateController@store');
		
		// update data post request
		Route::post('States/State/{stateAbb}', 'States\Controllers\StateController@update');
		
		//delete data delete request
		Route::delete('States/State/{stateAbb}', 'States\Controllers\StateController@Destroy');
			
    }
}


