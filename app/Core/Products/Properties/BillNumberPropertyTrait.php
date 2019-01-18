<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BillNumberPropertyTrait
{
	/**
     * @var billNo
     */
    private $billNo;
	/**
	 * @param String $billNo
	 */
	public function setBillNumber($billNo)
	{
		$this->billNo = $billNo;
	}
	/**
	 * @return billNo
	 */
	public function getBillNumber()
	{
		return $this->billNo;
	}
}