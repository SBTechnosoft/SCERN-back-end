<?php
namespace ERP\Api\V1_0\Reports\PolishReport\Routes;

use ERP\Api\V1_0\Reports\PolishReport\Controllers\PolishReportController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PolishReport implements RouteRegistrarInterface
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
			Route::get('Reports/PolishReport/PolishReport/company/{companyId}', 'Reports\PolishReport\Controllers\PolishReportController@getPolishReportData');
		});
		
	}
}


