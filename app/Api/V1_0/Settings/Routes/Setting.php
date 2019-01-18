<?php
namespace ERP\Api\V1_0\Settings\Routes;

use ERP\Api\V1_0\Settings\Controllers\SettingController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Setting implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
	
    public function register(RegistrarInterface $Registrar)
    {
    	Route::get('Settings/Setting/reminder', function () {
			/* php artisan migrate */
		    \Artisan::call('reminder');
		});
		// get remaining payment-data
		Route::get('Settings/Setting/payment', 'Settings\Controllers\SettingController@getPaymentData');
		
		// insert data post request
		Route::get('Settings/Setting', 'Settings\Controllers\SettingController@getData');
		
		// insert data post request
		Route::post('Settings/Setting', 'Settings\Controllers\SettingController@store');
		
		// update data post request
		Route::patch('Settings/Setting', 'Settings\Controllers\SettingController@update');
		
	}
}


