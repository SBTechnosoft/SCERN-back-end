<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionRateTypePropertyTrait
{
	/**
     * @var commissionRateType
     */
    private $commissionRateType;
	/**
	 * @param var $commissionRateType
	 */
	public function setCommissionRateType($commissionRateType)
	{
		$this->commissionRateType = $commissionRateType;
	}
	/**
	 * @return commissionRateType
	 */
	public function getCommissionRateType()
	{
		return $this->commissionRateType;
	}
}