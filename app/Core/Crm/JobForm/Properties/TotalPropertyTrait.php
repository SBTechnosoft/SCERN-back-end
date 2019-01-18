<?php
namespace ERP\Core\Crm\JobForm\Properties;

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
	 * @param int $total
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