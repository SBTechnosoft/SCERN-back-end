<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PurchaseIgstTrait
{
	/**
     * @var purchaseIgst
     */
    private $purchaseIgst;
	/**
	 * @param float $purchaseIgst
	 */
	public function setPurchaseIgst($purchaseIgst)
	{
		$this->purchaseIgst = $purchaseIgst;
	}
	/**
	 * @return purchaseIgst
	 */
	public function getPurchaseIgst()
	{
		return $this->purchaseIgst;
	}
}