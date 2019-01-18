<?php
namespace ERP\Api\V1_0\ProductCategories\Routes;

use ERP\Api\V1_0\ProductCategories\Controllers\ProductCategoryController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductCategory implements RouteRegistrarInterface
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
			Route::get('ProductCategories/ProductCategory/bulk', 'ProductCategories\Controllers\ProductCategoryController@getBulkData');
			Route::get('ProductCategories/ProductCategory/{productCategoryId?}', 'ProductCategories\Controllers\ProductCategoryController@getData');
		});
		
		// insert data post request
		Route::post('ProductCategories/ProductCategory', 'ProductCategories\Controllers\ProductCategoryController@store');
		Route::post('ProductCategories/ProductCategory/batch', 'ProductCategories\Controllers\ProductCategoryController@multipleDataStore');
		
		// update data post request
		Route::post('ProductCategories/ProductCategory/{productCategoryId}', 'ProductCategories\Controllers\ProductCategoryController@update');
		
		//delete data delete request
		Route::delete('ProductCategories/ProductCategory/{productCategoryId}', 'ProductCategories\Controllers\ProductCategoryController@Destroy');
			
    }
}


