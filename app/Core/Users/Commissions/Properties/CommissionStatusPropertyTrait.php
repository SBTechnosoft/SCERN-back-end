<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionStatusPropertyTrait
{
	/**
     * @var commissionStatus
     */
    private $commissionStatus;
	/**
	 * @param var $commissionStatus
	 */
	public function setCommissionStatus($commissionStatus)
	{
		$this->commissionStatus = $commissionStatus;
	}
	/**
	 * @return commissionStatus
	 */
	public function getCommissionStatus()
	{
		return $this->commissionStatus;
	}
}