<?php
namespace ERP\Core\ProductGroups\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductGrpDescPropertyTrait
{
	/**
     * @var productGrpDesc
     */
    private $productGrpDesc;
	/**
	 * @param int $productGrpDesc
	 */
	public function setProductGroupDescription($productGrpDesc)
	{
		$this->productGrpDesc = $productGrpDesc;
	}
	/**
	 * @return productGrpDesc
	 */
	public function getProductGroupDescription()
	{
		return $this->productGrpDesc;
	}
}