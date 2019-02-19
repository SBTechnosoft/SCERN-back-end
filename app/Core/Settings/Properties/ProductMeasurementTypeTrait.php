<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait ProductMeasurementTypeTrait
{
	/**
     * @var productMeasurementType
     */
    private $productMeasurementType;
	/**
	 * @param string $productMeasurementType
	 */
	public function setProductMeasurementType($productMeasurementType)
	{
		$this->productMeasurementType = $productMeasurementType;
	}
	/**
	 * @return productMeasurementType
	 */
	public function getProductMeasurementType()
	{
		return $this->productMeasurementType;
	}
}