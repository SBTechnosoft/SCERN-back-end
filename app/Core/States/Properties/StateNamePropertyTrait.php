<?php
namespace ERP\Core\States\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait StateNamePropertyTrait
{
	/**
     * @var stateAbb
     */
    private $stateName;
	/**
	 * @param int $stateName
	 */
	public function setStateName($stateName)
	{
		$this->stateName = $stateName;
	}
	/**
	 * @return stateName
	 */
	public function getStateName()
	{
		return $this->stateName;
	}
}