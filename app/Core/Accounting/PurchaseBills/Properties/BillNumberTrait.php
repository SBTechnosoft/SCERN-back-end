<?php
namespace ERP\Core\Accounting\PurchaseBills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BillNumberTrait
{
	/**
     * @var billNumber
     */
    private $billNumber;
	/**
	 * @param string $billNumber
	 */
	public function setBillNumber($billNumber)
	{
		$this->billNumber = $billNumber;
	}
	/**
	 * @return billNumber
	 */
	public function getBillNumber()
	{
		return $this->billNumber;
	}
}