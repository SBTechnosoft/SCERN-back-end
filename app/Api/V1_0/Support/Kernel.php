<?php
namespace ValuePad\Api\Support;

use Asm89\Stack\CorsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use ValuePad\Support\DefaultEnvironmentDetectorReplacerTrait;
use Exception;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Kernel extends HttpKernel
{
    use DefaultEnvironmentDetectorReplacerTrait;
	/**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [];


    /**
     * @param Application $app
     * @param Router $router
     */
    public function __construct(Application $app, Router $router)
    {
        $this->bootstrappers = $this->replaceDefaultDetectEnvironmentBootstrapper($this->bootstrappers);

        parent::__construct($app, $router);
    }

	/**
	 * @param Request $request
	 * @param Exception $e
	 * @return Response
	 */
	protected function renderException($request, Exception $e)
	{
		$response = parent::renderException($request, $e);

		/**
		 * @var CorsService $corsService
		 */
		$corsService = $this->app->make(CorsService::class);

		return $corsService->addActualRequestHeaders($response, $request);
	}
}
