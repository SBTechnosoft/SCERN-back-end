<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait NoOfDecimalPointsPropertyTrait
{
	/**
     * @var noOfDecimalPoints
     */
    private $noOfDecimalPoints;
	/**
	 * @param int $noOfDecimalPoints
	 */
	public function setNoOfDecimalPoints($noOfDecimalPoints)
	{
		$this->noOfDecimalPoints = $noOfDecimalPoints;
	}
	/**
	 * @return noOfDecimalPoints
	 */
	public function getNoOfDecimalPoints()
	{
		return $this->noOfDecimalPoints;
	}
}