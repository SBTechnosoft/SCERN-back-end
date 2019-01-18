<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait AdvanceSalesStatusTrait
{
	/**
     * @var advanceSales
     */
    private $advanceSales;
	/**
	 * @param string $advanceSales
	 */
	public function setAdvanceSalesStatus($advanceSales)
	{
		$this->advanceSales = $advanceSales;
	}
	/**
	 * @return advanceSales
	 */
	public function getAdvanceSalesStatus()
	{
		return $this->advanceSales;
	}
}