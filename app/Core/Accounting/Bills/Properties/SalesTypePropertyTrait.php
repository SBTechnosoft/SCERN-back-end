<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait SalesTypePropertyTrait
{
	/**
     * @var salesType
     */
    private $salesType;
	/**
	 * @param float $salesType
	 */
	public function setSalesType($salesType)
	{
		$this->salesType = $salesType;
	}
	/**
	 * @return salesType
	 */
	public function getSalesType()
	{
		return $this->salesType;
	}
}