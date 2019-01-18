<?php
namespace ERP\Api\V1_0\Companies\Routes;

use ERP\Api\V1_0\Companies\Controllers\CompanyController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Company implements RouteRegistrarInterface
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
			Route::get('Companies/Company/{companyId?}', 'Companies\Controllers\CompanyController@getData');
		});
		
		// insert data post request
		Route::post('Companies/Company', 'Companies\Controllers\CompanyController@store');
		
		// update data post request
		Route::post('Companies/Company/{companyId}', 'Companies\Controllers\CompanyController@update');
		
		//delete data delete request
		Route::delete('Companies/Company/{companyId}', 'Companies\Controllers\CompanyController@destroy');
			
    }
}


