<?php
namespace ERP\Api\V1_0\Reports\ReportBuilder\Routes;

use ERP\Api\V1_0\Reports\ReportBuilder\Controllers\ReportBuilderController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class ReportBuilder implements RouteRegistrarInterface
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
			Route::get('Reports/ReportBuilder/ReportBuilder', 'Reports\ReportBuilder\Controllers\ReportBuilderController@getAllData');
			Route::get('Reports/ReportBuilder/ReportBuilder/groups', 'Reports\ReportBuilder\Controllers\ReportBuilderController@getReportBuilderGroups');
			Route::get('Reports/ReportBuilder/ReportBuilder/groups/{groupId}', 'Reports\ReportBuilder\Controllers\ReportBuilderController@getTablesByGroup');
			Route::get('Reports/ReportBuilder/ReportBuilder/generate/{reportId}', 'Reports\ReportBuilder\Controllers\ReportBuilderController@generate');

			Route::get('Reports/ReportBuilder/ReportBuilder/{reportId}', 'Reports\ReportBuilder\Controllers\ReportBuilderController@getData');
		});

		Route::post('Reports/ReportBuilder/ReportBuilder', 'Reports\ReportBuilder\Controllers\ReportBuilderController@store');
		Route::post('Reports/ReportBuilder/ReportBuilder/preview', 'Reports\ReportBuilder\Controllers\ReportBuilderController@generatePreview');
		Route::post('Reports/ReportBuilder/ReportBuilder/{reportId}', 'Reports\ReportBuilder\Controllers\ReportBuilderController@update');

		Route::delete('Reports/ReportBuilder/ReportBuilder/{reportId}', 'Reports\ReportBuilder\Controllers\ReportBuilderController@destroy');
		
	}
}