<?php

namespace ERP\Api\V1_0\Support;



use Illuminate\Routing\Router;

use Illuminate\Http\Request;

use ERP\Http\Requests;

use Symfony\Component\Finder\Finder;

use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

use Illuminate\Routing\Router as IlluminateRouter;

/**

 * @author Reema Patel<reema.p@siliconbrain.in>

 */

class RouteServiceProvider extends ServiceProvider

{

	/**

     * @var namespace    

     */

	protected $namespace ="";

	/**

	 * below function is for going to the particular Routes file for get,post,patch and delete Request 

	 * @param Router $router	 

	 */

	protected function define(Router $router)

    {	
		date_default_timezone_set("Asia/Calcutta");
		if(isset($_SERVER['REQUEST_URI']))
    	{
			//splitting components from url
			$splitUri = explode("/", $_SERVER['REQUEST_URI']);
			$convertedString = str_replace(' ', '', ucwords(str_replace('-', ' ', $splitUri[1])));

			//accessing multiple components dynamically from url

			$controllerPath = 'ERP\Api\V1_0\\'.$convertedString.'\\Controllers';
			$router->group([ 

	            'namespace' => $controllerPath

	        ],function (Router $router) {

	            $packages = $this->app->make('config')->get('app.packages');

				$splitUriRoute = explode("/", $_SERVER['REQUEST_URI']); 

				
				$urlFlag=0;
				$routeArray = array();
				$routeArray['companies'] = "Company";
				$routeArray['branches'] = "Branch";
				$routeArray['states'] = "State";
				$routeArray['cities'] = "City";
				$routeArray['banks'] = "Bank";
				$routeArray['invoice-numbers'] = "Invoice";
				$routeArray['product-categories'] = "ProductCategory";
				$routeArray['product-groups'] = "ProductGroup";
				$routeArray['products'] = "Product";
				$routeArray['quotation-numbers'] = "Quotation";
				$routeArray['templates'] = "Template";
				$routeArray['commissions'] = "Commission";
				$routeArray['ledger-groups'] = "LedgerGroup";
				$routeArray['ledgers'] = "Ledger";
				$routeArray['journals'] = "Journal";
				$routeArray['clients'] = "Client";
				$routeArray['bills'] = "Bill";
				$routeArray['sales-returns'] = "SalesReturn";
				$routeArray['purchase-returns'] = "PurchaseReturn";
				$routeArray['trial-balance'] = "TrialBalance";
				$routeArray['balance-sheet'] = "BalanceSheet";
				$routeArray['profit-loss'] = "ProfitLoss";
				$routeArray['cash-flow'] = "CashFlow";
				$routeArray['users'] = "User";
				$routeArray['authenticate'] = "Authenticate";
				$routeArray['logout'] = "Logout";
				$routeArray['documents'] = "Document";
				$routeArray['taxation'] = "Taxation";
				$routeArray['polish-report'] = "PolishReport";
				$routeArray['job-form'] = "JobForm";
				$routeArray['job-form-number'] = "JobFormNumber";
				$routeArray['quotations'] = "Quotation";
				$routeArray['conversations'] = "Conversation";
				$routeArray['professions'] = "Profession";
				$routeArray['expenses'] = "Expense";
				$routeArray['purchase-bills'] = "PurchaseBill";
				$routeArray['measurement-units'] = "Measurement";
				$routeArray['settings'] = "Setting";
				$routeArray['merge'] = "Merge";
				$routeArray['report-builder'] = "ReportBuilder";
				$routeArray['credit-notes'] = "CreditNote";
				$routeArray['debit-notes'] = "DebitNote";
				$routeName = "";
				
				foreach($routeArray as $key => $value)

				{
					if($key==$splitUriRoute[1])

					{

						$routeName = $value;

						break;

					}

					else if($splitUriRoute[1]=="settings" || $splitUriRoute[1]=="accounting" || $splitUriRoute[1]=="reports" || $splitUriRoute[1]=="crm" || $splitUriRoute[1] == "users")

					{
						if(count($splitUriRoute)>2)
						{
							if($key==$splitUriRoute[2])

							{
								$urlFlag=1;

								$routeName = $value;

								break;

							}
						}

					}

				}
				
				if($urlFlag==0)

				{

					$convertedString1 = str_replace(' ', '', ucwords(str_replace('-', ' ', $splitUriRoute[1])));

					foreach ($packages as $package) {			

						//condition for going to particular route file as per url	

						if(!strcmp($package,$convertedString1)) 

						{

							$path = app_path('Api\V1_0\\' . str_replace('\\', '/', $package) .'\\Routes');		

							$namespace = 'ERP\Api\V1_0\\' . $package ;

							

							//go to the register method from particular Route class 

							$this->app->make($namespace .'\\Routes\\' . $routeName)

							->register($router);	

							break;

						}							

					}

				}

				else

				{

					if($splitUriRoute[1]=="settings")

					{

						$convertedString1 = str_replace(' ', '', ucwords(str_replace('-', ' ', $splitUriRoute[2])));

						foreach ($packages as $package) 

						{			

							//condition for going to particular route file as per url	

							if(!strcmp($package,$convertedString1)) 

							{

								$path = app_path('Api\V1_0\\' . str_replace('\\', '/', $package) .'\\Routes');	

								$namespace = 'ERP\Api\V1_0\\Settings\\' . $package ;		
								
								//go to the register method from particular Route class 

								$this->app->make($namespace .'\\Routes\\' . $routeName)

								->register($router);	

								break;

							}

						}

					}
					else if($splitUriRoute[1]=="users"){
						$convertedString1 = str_replace(' ', '', ucwords(str_replace('-', ' ', $splitUriRoute[2])));
						foreach ($packages as $package)
						{
							//condition for going to particular route file as per url
							if(!strcmp($package,$convertedString1)) {
								$path = app_path('Api\V1_0\\' . str_replace('\\', '/', $package) .'\\Routes');
								$namespace = 'ERP\Api\V1_0\\Users\\' . $package ;
								//go to the register method from particular Route class 
								$this->app->make($namespace .'\\Routes\\' . $routeName)->register($router);
								break;
							}

						}

					}
					else if($splitUriRoute[1]=="reports")

					{

						$convertedString1 = str_replace(' ', '', ucwords(str_replace('-', ' ', $splitUriRoute[2])));

						foreach ($packages as $package) 

						{			

							//condition for going to particular route file as per url	

							if(!strcmp($package,$convertedString1)) 

							{

								$path = app_path('Api\V1_0\\' . str_replace('\\', '/', $package) .'\\Routes');		

								$namespace = 'ERP\Api\V1_0\\Reports\\' . $package ;		

								//go to the register method from particular Route class 

								$this->app->make($namespace .'\\Routes\\' . $routeName)

								->register($router);	

								break;

							}							

						}

					}
					else if($splitUriRoute[1]=="crm")

					{

						$convertedString1 = str_replace(' ', '', ucwords(str_replace('-', ' ', $splitUriRoute[2])));

						foreach ($packages as $package) 

						{			

							//condition for going to particular route file as per url	

							if(!strcmp($package,$convertedString1)) 

							{

								$path = app_path('Api\V1_0\\' . str_replace('\\', '/', $package) .'\\Routes');		

								$namespace = 'ERP\Api\V1_0\\Crm\\' . $package ;		

								//go to the register method from particular Route class 

								$this->app->make($namespace .'\\Routes\\' . $routeName)

								->register($router);	

								break;

							}							

						}

					}

					else

					{
						$convertedString1 = str_replace(' ', '', ucwords(str_replace('-', ' ', $splitUriRoute[2])));

						foreach ($packages as $package) 

						{
							//condition for going to particular route file as per url	

							if(!strcmp($package,$convertedString1)) 

							{

								$path = app_path('Api\V1_0\\' . str_replace('\\', '/', $package) .'\\Routes');		

								$namespace = 'ERP\Api\V1_0\\Accounting\\' . $package ;

								

								//go to the register method from particular Route class 

								$this->app->make($namespace .'\\Routes\\' . $routeName)

								->register($router);	

								break;

							}							

						}
					}
				}	

					

	        });
		}
    }

	

    /**

     * @param IlluminateRouter $router

     */

    public function map(IlluminateRouter $router)	

    {		

        /**

         * @var Router $proxy

         */		 

		$proxy = $this->app->make(Router::class);		

        $this->define($proxy);

        $routes = $router->getRoutes();	

			

        foreach ($proxy->getRoutes() as $route) {	

			$routes->add($route);				

        }

		$router->setRoutes($routes);

    }

}