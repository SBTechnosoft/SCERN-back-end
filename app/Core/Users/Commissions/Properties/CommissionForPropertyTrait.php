<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionForPropertyTrait
{
	/**
     * @var commissionFor
     */
    private $commissionFor;
	/**
	 * @param int $templateType
	 */
	public function setCommissionFor($commissionFor)
	{
		$this->commissionFor = $commissionFor;
	}
	/**
	 * @return commissionFor
	 */
	public function getCommissionFor()
	{
		return $this->commissionFor;
	}
}