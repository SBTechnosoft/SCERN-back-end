<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait IsSalesOrderTrait
{
	/**
     * @var isSalesOrder
     */
    private $isSalesOrder;
	/**
	 * @param array $isSalesOrder
	 */
	public function setIsSalesOrder($isSalesOrder)
	{
		$this->isSalesOrder = $isSalesOrder;
	}
	/**
	 * @return isSalesOrder
	 */
	public function getIsSalesOrder()
	{
		return $this->isSalesOrder;
	}
}