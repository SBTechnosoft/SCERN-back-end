<?php
namespace ERP\Api\V1_0\Settings\Templates\Routes;

use ERP\Api\V1_0\Settings\Templates\Controllers\TemplateController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Template implements RouteRegistrarInterface
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
			Route::get('Settings/Templates/Template/{templateId?}', 'Settings\Templates\Controllers\TemplateController@getData');
			Route::get('Settings/Templates/Template/company/{companyId?}', 'Settings\Templates\Controllers\TemplateController@getTemplateData');
		});
		
		// insert data post request
		Route::post('Settings/Templates/Template', 'Settings\Templates\Controllers\TemplateController@store');
		
		// update data post request
		Route::post('Settings/Templates/Template/{templateId}', 'Settings\Templates\Controllers\TemplateController@update');
	}
}


