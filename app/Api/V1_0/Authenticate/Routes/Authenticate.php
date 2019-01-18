<?php
namespace ERP\Api\V1_0\Authenticate\Routes;

use ERP\Api\V1_0\Authenticate\Controllers\AuthenticateController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Authenticate implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		// get data get request
		Route::get('Authenticate/Authenticate', 'Authenticate\Controllers\AuthenticateController@getAllData');
		
		// get data get request
		Route::get('Authenticate/Authenticate/users/{userId}', 'Authenticate\Controllers\AuthenticateController@getData');
		
		// insert data post request
		Route::post('Authenticate/Authenticate', 'Authenticate\Controllers\AuthenticateController@store');
	}
}


