<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionToQtyPropertyTrait
{
	/**
     * @var commissionToQty
     */
    private $commissionToQty;
	/**
	 * @param int $templateType
	 */
	public function setCommissionToQty($commissionToQty)
	{
		$this->commissionToQty = $commissionToQty;
	}
	/**
	 * @return commissionToQty
	 */
	public function getCommissionToQty()
	{
		return $this->commissionToQty;
	}
}