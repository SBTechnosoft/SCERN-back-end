<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductArrayPropertyTrait
{
	/**
     * @var productArray
     */
    private $productArray;
	/**
	 * @param string $productArray
	 */
	public function setProductArray($productArray)
	{
		$this->productArray = $productArray;
	}
	/**
	 * @return productArray
	 */
	public function getProductArray()
	{
		return $this->productArray;
	}
}