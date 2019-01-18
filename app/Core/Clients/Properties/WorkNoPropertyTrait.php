<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait WorkNoPropertyTrait
{
	/**
     * @var workNo
     */
    private $workNo;
	/**
	 * @param int $workNo
	 */
	public function setWorkNo($workNo)
	{
		$this->workNo = $workNo;
	}
	/**
	 * @return workNo
	 */
	public function getWorkNo()
	{
		return $this->workNo;
	}
}