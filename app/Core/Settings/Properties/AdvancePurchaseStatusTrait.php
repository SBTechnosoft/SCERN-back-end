<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait AdvancePurchaseStatusTrait
{
	/**
     * @var advancePurchase
     */
    private $advancePurchase;
	/**
	 * @param string $advancePurchase
	 */
	public function setAdvancePurchaseStatus($advancePurchase)
	{
		$this->advancePurchase = $advancePurchase;
	}
	/**
	 * @return advancePurchase
	 */
	public function getAdvancePurchaseStatus()
	{
		return $this->advancePurchase;
	}
}