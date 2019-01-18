<?php
namespace ERP\Core\Users\Commissions\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CommissionIdPropertyTrait
{
	/**
     * @var commissionId
     */
    private $commissionId;
	/**
	 * @param int $templateType
	 */
	public function setCommissionId($commissionId)
	{
		$this->commissionId = $commissionId;
	}
	/**
	 * @return commissionId
	 */
	public function getCommissionId()
	{
		return $this->commissionId;
	}
}