<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionCalcOnPropertyTrait
{
	/**
     * @var commissionCalcOn
     */
    private $commissionCalcOn;
	/**
	 * @param var $commissionCalcOn
	 */
	public function setCommissionCalcOn($commissionCalcOn)
	{
		$this->commissionCalcOn = $commissionCalcOn;
	}
	/**
	 * @return commissionCalcOn
	 */
	public function getCommissionCalcOn()
	{
		return $this->commissionCalcOn;
	}
}