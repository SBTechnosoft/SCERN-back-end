<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionRatePropertyTrait
{
	/**
     * @var commissionRate
     */
    private $commissionRate;
	/**
	 * @param decimal $commissionRate
	 */
	public function setCommissionRate($commissionRate)
	{
		$this->commissionRate = $commissionRate;
	}
	/**
	 * @return commissionRate
	 */
	public function getCommissionRate()
	{
		return $this->commissionRate;
	}
}