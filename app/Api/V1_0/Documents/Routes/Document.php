<?php
namespace ERP\Api\V1_0\Documents\Routes;

use ERP\Api\V1_0\Documents\Controllers\DocumentController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Document implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		ini_set('memory_limit', '256M');
		//post request 
		Route::post('Documents/Document/bill', 'Documents\Controllers\DocumentController@getData');
		
		//delete request
		Route::delete('Documents/Document/{documentId}', 'Documents\Controllers\DocumentController@deleteDocument');
	}
}


