<?php
namespace ERP\Core\ProductGroups\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductGrpParentIdPropertyTrait
{
	/**
     * @var productParentGrpId
     */
    private $productParentGrpId;
	/**
	 * @param int $productParentGrpId
	 */
	public function setProductGroupParentId($productParentGrpId)
	{
		$this->productParentGrpId = $productParentGrpId;
	}
	/**
	 * @return productParentGrpId
	 */
	public function getProductGroupParentId()
	{
		return $this->productParentGrpId;
	}
}