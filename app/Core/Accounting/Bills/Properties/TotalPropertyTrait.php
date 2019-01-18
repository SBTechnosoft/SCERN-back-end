<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TotalPropertyTrait
{
	/**
     * @var total
     */
    private $total;
	/**
	 * @param float $total
	 */
	public function setTotal($total)
	{
		$this->total = $total;
	}
	/**
	 * @return total
	 */
	public function getTotal()
	{
		return $this->total;
	}
}