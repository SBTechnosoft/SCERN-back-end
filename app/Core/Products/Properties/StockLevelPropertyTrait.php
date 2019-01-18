<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait StockLevelPropertyTrait
{
	/**
     * @var stockLevel
     */
    private $stockLevel;
	/**
	 * @param float $stockLevel
	 */
	public function setMinimumStockLevel($stockLevel)
	{
		$this->stockLevel = $stockLevel;
	}
	/**
	 * @return stockLevel
	 */
	public function getMinimumStockLevel()
	{
		return $this->stockLevel;
	}
}