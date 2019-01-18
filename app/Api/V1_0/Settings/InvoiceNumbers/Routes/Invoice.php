<?php
namespace ERP\Api\V1_0\Settings\InvoiceNumbers\Routes;

use ERP\Api\V1_0\Settings\InvoiceNumbers\Controllers\InvoiceController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Invoice implements RouteRegistrarInterface
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
			Route::get('Settings/InvoiceNumbers/Invoice/{invoiceId?}','Settings\InvoiceNumbers\Controllers\InvoiceController@getData');
			Route::get('Settings/InvoiceNumbers/Invoice/company/{companyId}', 'Settings\InvoiceNumbers\Controllers\InvoiceController@getAllData');
			Route::get('Settings/InvoiceNumbers/Invoice/company/{companyId?}/latest', 'Settings\InvoiceNumbers\Controllers\InvoiceController@getLatestData');
		});
		
		// insert data post request
		Route::post('Settings/InvoiceNumbers/Invoice', 'Settings\InvoiceNumbers\Controllers\InvoiceController@store');
		
		// update data request
		Route::post('Settings/InvoiceNumbers/Invoice/{invoiceId}', 'Settings\InvoiceNumbers\Controllers\InvoiceController@update');
	}
}


