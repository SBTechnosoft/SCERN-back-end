<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ExtraChargeTrait
{
	/**
     * @var extraCharge
     */
    private $extraCharge;
	/**
	 * @param float $extraCharge
	 */
	public function setExtraCharge($extraCharge)
	{
		$this->extraCharge = $extraCharge;
	}
	/**
	 * @return extraCharge
	 */
	public function getExtraCharge()
	{
		return $this->extraCharge;
	}
}