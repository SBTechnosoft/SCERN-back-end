<?php
namespace ERP\Core\States\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait StateCodeTrait
{
	/**
     * @var stateCode
     */
    private $stateCode;
	/**
	 * @param int $stateCode
	 */
	public function setStateCode($stateCode)
	{
		$this->stateCode = $stateCode;
	}
	/**
	 * @return stateCode
	 */
	public function getStateCode()
	{
		return $this->stateCode;
	}
}