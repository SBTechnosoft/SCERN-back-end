<?php
namespace ERP\Debug\Support;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use ERP\Debug\Controllers\LinkController;
use ERP\Debug\Controllers\PushController;
use ERP\Debug\Controllers\ResetController;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class RouteServiceProvider extends ServiceProvider
{
	/**
	 * @param Router $router
	 */
	public function map(Router $router)
	{
		$router->group(['prefix' => 'debug'], function(Router $router){
			$router->get('reset', ResetController::class.'@reset');

			$router->post('link', LinkController::class.'@store');

			$router->get('push', PushController::class.'@index');
			$router->post('push', PushController::class.'@store');
			$router->delete('push', PushController::class.'@destroy');
		});
	}
}