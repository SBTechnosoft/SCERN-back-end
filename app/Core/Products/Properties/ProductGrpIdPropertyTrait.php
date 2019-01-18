<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductGrpIdPropertyTrait
{
	/**
     * @var ProductGrp
     */
    private $ProductGrpId;
	/**
	 * @param int $ProductGrp
	 */
	public function setProductGroupId($ProductGrp)
	{
		$this->ProductGrp = $ProductGrp;
	}
	/**
	 * @return ProductGrp
	 */
	public function getProductGroupId()
	{
		return $this->ProductGrp;
	}
}