<?php
namespace ERP\Core\ProductGroups\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductGroupNamePropertyTrait
{
	/**
     * @var productParentGrpName
     */
    private $productParentGrpName;
	/**
	 * @param int $productParentGrpName
	 */
	public function setProductGroupName($productParentGrpName)
	{
		$this->productParentGrpName = $productParentGrpName;
	}
	/**
	 * @return productParentGrpName
	 */
	public function getProductGroupName()
	{
		return $this->productParentGrpName;
	}
}