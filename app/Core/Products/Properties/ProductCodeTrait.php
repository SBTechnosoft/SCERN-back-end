<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductCodeTrait
{
	/**
     * @var BarcodeNo
     */
    private $ProductCode;
	/**
	 * @param float $ProductCode
	 */
	public function setProductCode($ProductCode)
	{
		$this->ProductCode = $ProductCode;
	}
	/**
	 * @return ProductCode
	 */
	public function getProductCode()
	{
		return $this->ProductCode;
	}
}