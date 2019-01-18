<?php
namespace ERP\Api\V1_0\Logout\Routes;

use ERP\Api\V1_0\Logout\Controllers\LogoutController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Logout implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		//delete data delete request
		Route::delete('Logout/Logout/user/{userId}', 'Logout\Controllers\LogoutController@Destroy');
			
    }
}


