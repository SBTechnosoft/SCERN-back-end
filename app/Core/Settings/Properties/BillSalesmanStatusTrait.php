<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BillSalesmanStatusTrait
{
	/**
     * @var salesman
     */
    private $salesman;
	/**
	 * @param string $salesman
	 */
	public function setBillSalesmanStatus($salesman)
	{
		$this->salesman = $salesman;
	}
	/**
	 * @return salesman
	 */
	public function getBillSalesmanStatus()
	{
		return $this->salesman;
	}
}