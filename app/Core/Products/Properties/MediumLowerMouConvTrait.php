<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait MediumLowerMouConvTrait
{
	/**
     * @var mediumLowerMouConv
     */
    private $mediumLowerMouConv;
	/**
	 * @param Integer $mediumLowerMouConv
	 */
	public function setMediumLowerMouConv($mediumLowerMouConv)
	{
		$this->mediumLowerMouConv = $mediumLowerMouConv;
	}
	/**
	 * @return mediumLowerMouConv
	 */
	public function getMediumLowerMouConv()
	{
		return $this->mediumLowerMouConv;
	}
}