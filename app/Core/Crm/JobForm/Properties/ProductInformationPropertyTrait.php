<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductInformationPropertyTrait
{
	/**
     * @var productInformation
     */
    private $productInformation;
	/**
	 * @param int $productInformation
	 */
	public function setProductInformation($productInformation)
	{
		$this->productInformation = $productInformation;
	}
	/**
	 * @return productInformation
	 */
	public function getProductInformation()
	{
		return $this->productInformation;
	}
}