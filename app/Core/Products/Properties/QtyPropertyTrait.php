<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait QtyPropertyTrait
{
	/**
     * @var qty
     */
    private $qty;
	/**
	 * @param float $qty
	 */
	public function setQty($qty)
	{
		$this->qty = $qty;
	}
	/**
	 * @return qty
	 */
	public function getQty()
	{
		return $this->qty;
	}
}