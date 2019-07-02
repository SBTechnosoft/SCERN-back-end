<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait ItemCodeTrait
{
	/**
     * @var ItemCode
     */
    private $itemCode;
	/**
	 * @param float $itemCode
	 */
	public function setItemCode($itemCode)
	{
		$this->itemCode = $itemCode;
	}
	/**
	 * @return ItemCode
	 */
	public function getItemCode()
	{
		return $this->itemCode;
	}
}