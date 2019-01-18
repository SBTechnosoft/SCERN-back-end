<?php
namespace ERP\Api\V1_0\Crm\JobFormNumber\Routes;

use ERP\Api\V1_0\Crm\JobFormNumber\Controllers\JobFormNumberController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormNumber implements RouteRegistrarInterface
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
			Route::get('Crm/JobFormNumber/JobFormNumber', 'Crm\JobFormNumber\Controllers\JobFormNumberController@getAllData');
			Route::get('Crm/JobFormNumber/JobFormNumber/company/{companyId}/latest', 'Crm\JobFormNumber\Controllers\JobFormNumberController@getLatestData');
		});
		
		// insert data post request
		Route::post('Crm/JobFormNumber/JobFormNumber', 'Crm\JobFormNumber\Controllers\JobFormNumberController@store');
		
		// update data post request
		// Route::post('Companies/Company/{companyId}', 'Companies\Controllers\CompanyController@update');
		
		//delete data delete request
		// Route::delete('Companies/Company/{companyId}', 'Companies\Controllers\CompanyController@destroy');
			
    }
}


