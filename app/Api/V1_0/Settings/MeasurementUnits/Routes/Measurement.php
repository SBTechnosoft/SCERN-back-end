<?php
namespace ERP\Api\V1_0\Settings\MeasurementUnits\Routes;

use ERP\Api\V1_0\Settings\MeasurementUnits\Controllers\MeasurementController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class Measurement implements RouteRegistrarInterface
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
			Route::get('Settings/MeasurementUnits/Measurement/{measurementUnitId?}', 'Settings\MeasurementUnits\Controllers\MeasurementController@getData');
			// Route::get('Settings/MeasurementUnits/Measurement/{measurementUnitId?}', ['uses' => 'Settings\MeasurementUnits\Controllers\MeasurementController@getData']);
		});
		
		// insert data post request
		Route::post('Settings/MeasurementUnits/Measurement', 'Settings\MeasurementUnits\Controllers\MeasurementController@store');

		// update data post request
		Route::post('Settings/MeasurementUnits/Measurement/{measurementUnitId}', 'Settings\MeasurementUnits\Controllers\MeasurementController@update');

		//delete data -- delete request
		// Route::delete('Settings/MeasurementUnits/Measurement/{measurementUnitId}', 'Settings\MeasurementUnits\Controllers\MeasurementController@destroy');
		Route::delete('Settings/MeasurementUnits/Measurement/{measurementUnitId}', ['uses' => 'Settings\MeasurementUnits\Controllers\MeasurementController@destroy']);
		// Route::delete('Settings/MeasurementUnits/Measurement/{measurementUnitId}', 'MeasurementController@destroy');
	}
}
