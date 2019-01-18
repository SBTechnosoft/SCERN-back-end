<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BillArrayPropertyTrait
{
	/**
     * @var billArray
     */
    private $billArray;
	/**
	 * @param float $billArray
	 */
	public function setBillArray($billArray)
	{
		$this->billArray = $billArray;
	}
	/**
	 * @return billArray
	 */
	public function getBillArray()
	{
		return $this->billArray;
	}
}