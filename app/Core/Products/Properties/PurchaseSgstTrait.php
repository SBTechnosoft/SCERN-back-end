<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PurchaseSgstTrait
{
	/**
     * @var purchaseSgst
     */
    private $purchaseSgst;
	/**
	 * @param float $purchaseSgst
	 */
	public function setPurchaseSgst($purchaseSgst)
	{
		$this->purchaseSgst = $purchaseSgst;
	}
	/**
	 * @return purchaseSgst
	 */
	public function getPurchaseSgst()
	{
		return $this->purchaseSgst;
	}
}