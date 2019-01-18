<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionTypePropertyTrait
{
	/**
     * @var commissionType
     */
    private $commissionType;
	/**
	 * @param var $commissionType
	 */
	public function setCommissionType($commissionType)
	{
		$this->commissionType = $commissionType;
	}
	/**
	 * @return commissionType
	 */
	public function getCommissionType()
	{
		return $this->commissionType;
	}
}