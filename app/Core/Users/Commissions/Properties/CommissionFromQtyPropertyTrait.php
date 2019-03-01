<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionFromQtyPropertyTrait
{
	/**
     * @var commissionFromQty
     */
    private $commissionFromQty;
	/**
	 * @param int $commissionFromQty
	 */
	public function setCommissionFromQty($commissionFromQty)
	{
		$this->commissionFromQty = $commissionFromQty;
	}
	/**
	 * @return commissionFromQty
	 */
	public function getCommissionFromQty()
	{
		return $this->commissionFromQty;
	}
}