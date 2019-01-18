<?php
namespace ERP\Api\V1_0\Support;

use ERP\Api\V1_0\Support\Validation\Rules\DocumentMixedIdentifier;
use ERP\Api\V1_0\Support\Validation\Rules\TraversableCast;
use ERP\Core\Shared\Options\UpdateOptions;
use RuntimeException;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
abstract class BaseProcessor
{
    /**
     * @param Binder $binder
     */
    protected function rules(Binder $binder)
    {
        foreach ($this->configuration() as $name => $rule) 
		{
            $binder->bind($name, $this->createRootInflator($rule));
        }
    }
	/**
	 * @param string|array $rule
	 * @return callable
	 */
	private function createRootInflator($rule)
	{
		return function (Property $property) use($rule) {

			if (is_string($rule) && ends_with($rule, '[]')){
				$rule = [cut_string_right($rule, '[]')];
			}
			if (is_array($rule) && count($rule) === 1 && array_key_exists(0, $rule)){
				$property->addRule(new TraversableCast());
				$property->addRule(new Each(function() use ($rule){
					return $this->resolveRule(current($rule));
				}));
			} else {
				$property->addRule($this->resolveRule($rule));
			}
		};
	}
    /**
     *
     * @param mixed $rule
     * @return RuleInterface
     */
    private function resolveRule($rule)
    {
        if (is_string($rule)) {
            $rule = $this->mapRules()[$rule];
        }

        if (is_callable($rule)) {
            return call_user_func($rule);
        }

        if (is_object($rule)) {
            return $rule;
        }

        if (is_string($rule)) {
            return new $rule();
        }

        if (is_array($rule)) {
            return new Walk(function (Binder $binder) use($rule) {
                foreach ($rule as $key => $value) {
                    $binder->bind($key, $this->createRootInflator($value));
                }
            });
        }
        throw new RuntimeException('Unable to resolve a validation rule.');
    }

    /**
     *
     * @return array
     */
    protected function mapRules()
    {
        return [
            'string' => StringCast::class,
            'bool' => BooleanCast::class,
            'int' => IntegerCast::class,
            'float' => FloatCast::class,
            'datetime' => Moment::class,
            'document' => DocumentMixedIdentifier::class
        ];
    }
    /**
     *
     * @return array
     */
    protected function configuration()
    {
        return [];
    }
    /**
     *
     * @return array
     */
    protected function allowable()
    {
        return array_keys($this->configuration());
    }
    /**
     *
     * @param UpdateOptions $options
     * @return UpdateOptions
     */
    public function schedulePropertiesToClear(UpdateOptions $options = null)
    {
        if ($options === null) {
            $options = new UpdateOptions();
        }
        $options->schedulePropertiesToClear($this->getFieldsWithNulls());

        return $options;
    }
}