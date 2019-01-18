<?php
namespace ERP\Core\Accounting\PurchaseBills\Properties;

/**
 * @author Farhan Shaikh<farhan.sp@siliconbrain.in>
 */
trait IsPurchaseOrderTrait
{
	/**
     * @var isPurchaseOrder
     */
    private $isPurchaseOrder;
	/**
	 * @param int $isPurchaseOrder
	 */
	public function setIsPurchaseOrder($isPurchaseOrder)
	{
		$this->isPurchaseOrder = $isPurchaseOrder;
	}
	/**
	 * @return isPurchaseOrder
	 */
	public function getIsPurchaseOrder()
	{
		return $this->isPurchaseOrder;
	}
}