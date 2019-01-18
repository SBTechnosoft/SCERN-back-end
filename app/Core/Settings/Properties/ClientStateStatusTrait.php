<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ClientStateStatusTrait
{
	/**
     * @var state
     */
    private $state;
	/**
	 * @param string $state
	 */
	public function setClientStateStatus($state)
	{
		$this->state = $state;
	}
	/**
	 * @return state
	 */
	public function getClientStateStatus()
	{
		return $this->state;
	}
}