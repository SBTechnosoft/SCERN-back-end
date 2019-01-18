<?php
namespace ERP\Core\States\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait StateAbbPropertyTrait
{
	/**
     * @var stateAbb
     */
    private $stateAbb;
	/**
	 * @param int $stateAbb
	 */
	public function setStateAbb($stateAbb)
	{
		$this->stateAbb = $stateAbb;
	}
	/**
	 * @return stateAbb
	 */
	public function getStateAbb()
	{
		return $this->stateAbb;
	}
}