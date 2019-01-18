<?php
namespace ERP\Api\V1_0\ProductGroups\Routes;

use ERP\Api\V1_0\ProductGroups\Controllers\ProductGroupController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductGroup implements RouteRegistrarInterface
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
			Route::get('ProductGroups/ProductGroup/bulk', 'ProductGroups\Controllers\ProductGroupController@getBulkData');
			Route::get('ProductGroups/ProductGroup/{productGroupId?}', 'ProductGroups\Controllers\ProductGroupController@getData');
		});
		
		// insert data post request
		Route::post('ProductGroups/ProductGroup', 'ProductGroups\Controllers\ProductGroupController@store');
		Route::post('ProductGroups/ProductGroup/batch', 'ProductGroups\Controllers\ProductGroupController@multipleDataStore');
		
		// update data post request
		Route::post('ProductGroups/ProductGroup/{productGroupId}', 'ProductGroups\Controllers\ProductGroupController@update');
		
		//delete data delete request
		Route::delete('ProductGroups/ProductGroup/{productGroupId}', 'ProductGroups\Controllers\ProductGroupController@Destroy');
			
    }
}


