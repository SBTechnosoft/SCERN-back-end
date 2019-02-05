<?php
namespace ERP\Api\V1_0\Products\Routes;

use ERP\Api\V1_0\Products\Controllers\ProductController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Product implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
    	ini_set('memory_limit', '256M');
    	
		// all the possible get request 
		Route::group(['as' => 'get'], function ()
		{
			Route::get('Products/Product/{productId?}', 'Products\Controllers\ProductController@getData');
			Route::get('Products/Product/{productId}/document', 'Products\Controllers\ProductController@getProductDocumentData');
			Route::get('Products/Product/{productId}/quantity-pricing', 'Products\Controllers\ProductController@getProductQuantityPricingData');
			Route::get('Products/Product/company/{companyId?}/branch/{branchId?}', 'Products\Controllers\ProductController@getAllProductData');
			Route::get('Products/Product/company/{companyId}', 'Products\Controllers\ProductController@getProductData');
			Route::get('Products/Product/company/{companyId}/transaction/details', 'Products\Controllers\ProductController@getStockDocumentPath');
			Route::get('Products/Product/company/{companyId}/priceList', 'Products\Controllers\ProductController@getPriceListDocumentPath');
			Route::get('Products/Product/company/{companyId}/transaction', 'Products\Controllers\ProductController@getProductTransactionData');
			Route::get('Products/Product/company/{companyId}/stock-summary', 'Products\Controllers\ProductController@getStockSummaryData');
			Route::get('Products/Product/itemize-summary/{productId}', 'Products\Controllers\ProductController@getItemizeSummaryData');
		});
		
		// insert data post request
		Route::post('Products/Product', 'Products\Controllers\ProductController@store');
		Route::post('Products/Product/inward', 'Products\Controllers\ProductController@inwardStore');
		Route::post('Products/Product/outward', 'Products\Controllers\ProductController@outwardStore');
		Route::post('Products/Product/batch', 'Products\Controllers\ProductController@multipleDataStore');
		Route::post('Products/Product/batchupdate', 'Products\Controllers\ProductController@batchUpdate');
		// update data post request
		Route::post('Products/Product/{productId}', 'Products\Controllers\ProductController@update');
		
		
		//delete data delete request
		Route::delete('Products/Product/{productId}', 'Products\Controllers\ProductController@Destroy');
	}
}


