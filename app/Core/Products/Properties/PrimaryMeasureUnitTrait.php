<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait PrimaryMeasureUnitTrait
{
	/**
     * @var primaryMeasureUnit
     */
    private $primaryMeasureUnit;
	/**
	 * @param float $primaryMeasureUnit
	 */
	public function setPrimaryMeasureUnit($primaryMeasureUnit)
	{
		$this->primaryMeasureUnit = $primaryMeasureUnit;
	}
	/**
	 * @return primaryMeasureUnit
	 */
	public function getPrimaryMeasureUnit()
	{
		return $this->primaryMeasureUnit;
	}
}