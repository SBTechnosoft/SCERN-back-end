<?php
namespace ERP\Api\V1_0\settings\QuotationNumbers\Routes;

use ERP\Api\V1_0\settings\QuotationNumbers\Controllers\QuotationController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Quotation implements RouteRegistrarInterface
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
			Route::get('Settings/QuotationNumbers/Quotation/{quotationId?}', 'Settings\QuotationNumbers\Controllers\QuotationController@getData');
			Route::get('Settings/QuotationNumbers/Quotation/company/{companyId?}', 'Settings\QuotationNumbers\Controllers\QuotationController@getAllData');
			Route::get('Settings/QuotationNumbers/Quotation/company/{companyId?}/latest', 'Settings\QuotationNumbers\Controllers\QuotationController@getLatestData');
		});
		// insert data post request
		Route::post('Settings/QuotationNumbers/Quotation', 'Settings\QuotationNumbers\Controllers\QuotationController@store');
		
		// update data post request
		Route::post('Settings/QuotationNumbers/Quotation/{quotationId}', 'Settings\QuotationNumbers\Controllers\QuotationController@update');
	}
}


