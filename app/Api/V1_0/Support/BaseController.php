<?php
namespace ERP\Api\V1_0\Support;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RuntimeException;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BaseController extends Controller
{
    /**
     * @var Container
     * @var ResourceManager
     */
    protected $container;
	protected $resource;
	/**
     * BaseController constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
		$this->container = $container;
        if (method_exists($this, 'initialize')) {
			$this->container->call([
                $this,
                'initialize'
            ]);
        }
	}
    /**
     * @param string $class
     * @return AbstractTransformer
     * @throws RuntimeException
     */
    protected function transformer($class)
    {
		$transformer = $this->container->make($class);

        if (! $transformer instanceof AbstractTransformer) {
            throw new RuntimeException('The transformer should be instance of AbstractTransformer');
        }
        $config = $this->container->make('config');
        /**
         * @var Request $request
         */
        $request = $this->container->make(Request::class);
        $input = $request->header('Include');
        $fields = $input ? array_map('trim', explode(',', $input)) : [];
        $calculatedProperties = [];
        foreach ($config->get('transformer.calculatedProperties', []) as $target => $options) {
            foreach ($options as $field => $callback) {
                $calculatedProperties[$target] = [
                    $field => $this->container->make($callback)
                ];
            }
        }
        $transformer
			->setCalculatedProperties($calculatedProperties)
			->setSpecifications($config->get('transformer.specifications'))
            ->setDefaults($config->get('transformer.include.default', []))
            ->setIncludes($fields)
            ->setIgnores($config->get('transformer.include.ignore', []));
        return $transformer;
    }
}
