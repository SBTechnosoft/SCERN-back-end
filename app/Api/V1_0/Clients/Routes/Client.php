<?php
namespace ERP\Api\V1_0\Clients\Routes;

use ERP\Api\V1_0\Clients\Controllers\ClientController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Client implements RouteRegistrarInterface
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
			Route::get('Clients/Client/{clientId?}', 'Clients\Controllers\ClientController@getData');
		});
		// insert data post request
		Route::post('Clients/Client', 'Clients\Controllers\ClientController@store');
		Route::post('Clients/Client/{clientId}', 'Clients\Controllers\ClientController@updateData');
	}
}


