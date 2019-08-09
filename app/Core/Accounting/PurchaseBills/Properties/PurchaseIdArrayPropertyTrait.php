<?php
namespace ERP\Core\Accounting\PurchaseBills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PurchaseIdArrayPropertyTrait
{
	/**
     * @var purchaseId
     */
    private $purchaseId;
	/**
	 * @param string $purchaseId
	 */
	public function setPurchaseId($purchaseId)
	{
		$this->purchaseId = $purchaseId;
	}
	/**
	 * @return purchaseId
	 */
	public function getPurchaseId()
	{
		return $this->purchaseId;
	}
}