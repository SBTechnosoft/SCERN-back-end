<?php
namespace ERP\Core\ProductGroups\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductGrpIdPropertyTrait
{
	/**
     * @var productGrpId
     */
    private $productGrpId;
	/**
	 * @param int $productGrpId
	 */
	public function setProductGroupId($productGrpId)
	{
		$this->productGrpId = $productGrpId;
	}
	/**
	 * @return productGrpId
	 */
	public function getProductGroupId()
	{
		return $this->productGrpId;
	}
}