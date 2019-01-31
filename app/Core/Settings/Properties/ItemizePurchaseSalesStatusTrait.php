<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait ItemizePurchaseSalesStatusTrait
{
	/**
     * @var itemizePurchaseSalesStatus
     */
    private $itemizePurchaseSalesStatus;
	/**
	 * @param string $itemizePurchaseSalesStatus
	 */
	public function setItemizePurchaseSalesStatus($itemizePurchaseSalesStatus)
	{
		$this->itemizePurchaseSalesStatus = $itemizePurchaseSalesStatus;
	}
	/**
	 * @return itemizePurchaseSalesStatus
	 */
	public function getItemizePurchaseSalesStatus()
	{
		return $this->itemizePurchaseSalesStatus;
	}
}