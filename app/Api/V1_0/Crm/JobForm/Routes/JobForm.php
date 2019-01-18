<?php
namespace ERP\Api\V1_0\Crm\JobForm\Routes;

use ERP\Api\V1_0\Crm\JobForm\Controllers\JobFormController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobForm implements RouteRegistrarInterface
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
			Route::get('Crm/JobForm/JobForm/{jobCardNo?}', 'Crm\JobForm\Controllers\JobFormController@getAllData');
		});
		
		// insert data post request
		Route::post('Crm/JobForm/JobForm', 'Crm\JobForm\Controllers\JobFormController@store');
		
		// update data post request
		// Route::post('Companies/Company/{companyId}', 'Companies\Controllers\CompanyController@update');
		
		//delete data delete request
		// Route::delete('Companies/Company/{companyId}', 'Companies\Controllers\CompanyController@destroy');
			
    }
}


