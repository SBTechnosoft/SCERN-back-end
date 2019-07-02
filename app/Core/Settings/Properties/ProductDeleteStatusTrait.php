<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait ProductDeleteStatusTrait
{
	/**
     * @var productDeleteStatus
     */
    private $productDeleteStatus;
	/**
	 * @param string $productDeleteStatus
	 */
	public function setProductDeleteStatus($productDeleteStatus)
	{
		$this->productDeleteStatus = $productDeleteStatus;
	}
	/**
	 * @return productDeleteStatus
	 */
	public function getProductDeleteStatus()
	{
		return $this->productDeleteStatus;
	}
}