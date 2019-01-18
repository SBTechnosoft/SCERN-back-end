<?php
namespace ERP\Core\Crm\JobForm\Properties;

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
	 * @param int $qty
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