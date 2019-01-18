<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait MaxSaleQtyTrait
{
	/**
     * @var maxSaleQty
     */
    private $maxSaleQty;
	/**
	 * @param float $maxSaleQty
	 */
	public function setMaxSaleQty($maxSaleQty)
	{
		$this->maxSaleQty = $maxSaleQty;
	}
	/**
	 * @return maxSaleQty
	 */
	public function getMaxSaleQty()
	{
		return $this->maxSaleQty;
	}
}