<?php
namespace ERP\Core\Accounting\PurchaseBills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BillTypeTrait
{
	/**
     * @var billType
     */
    private $billType;
	/**
	 * @param string $billType
	 */
	public function setBillType($billType)
	{
		$this->billType = $billType;
	}
	/**
	 * @return billType
	 */
	public function getBillType()
	{
		return $this->billType;
	}
}