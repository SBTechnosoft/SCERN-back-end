<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait LabourChargePropertyTrait
{
	/**
     * @var labourCharge
     */
    private $labourCharge;
	/**
	 * @param int $labourCharge
	 */
	public function setLabourCharge($labourCharge)
	{
		$this->labourCharge = $labourCharge;
	}
	/**
	 * @return labourCharge
	 */
	public function getLabourCharge()
	{
		return $this->labourCharge;
	}
}