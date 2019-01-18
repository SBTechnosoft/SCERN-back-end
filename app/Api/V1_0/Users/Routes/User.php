<?php
namespace ERP\Api\V1_0\Users\Routes;

use ERP\Api\V1_0\User\Controllers\UserController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class User implements RouteRegistrarInterface
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
			Route::get('Users/User/{userId?}', 'Users\Controllers\UserController@getData');
		});
		// insert data post request
		Route::post('Users/User', 'Users\Controllers\UserController@store');
		
		// update data post request
		Route::post('Users/User/{userId}', 'Users\Controllers\UserController@update');
		
		//delete data delete request
		Route::delete('Users/User/{userId}', 'Users\Controllers\UserController@Destroy');
    }
}


