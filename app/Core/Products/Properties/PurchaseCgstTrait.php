<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PurchaseCgstTrait
{
	/**
     * @var purchaseCgst
     */
    private $purchaseCgst;
	/**
	 * @param float $purchaseCgst
	 */
	public function setPurchaseCgst($purchaseCgst)
	{
		$this->purchaseCgst = $purchaseCgst;
	}
	/**
	 * @return purchaseCgst
	 */
	public function getPurchaseCgst()
	{
		return $this->purchaseCgst;
	}
}