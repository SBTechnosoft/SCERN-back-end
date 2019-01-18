<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait MediumMouConvTrait
{
	/**
     * @var mediumMouConv
     */
    private $mediumMouConv;
	/**
	 * @param Integer $mediumMouConv
	 */
	public function setMediumMouConv($mediumMouConv)
	{
		$this->mediumMouConv = $mediumMouConv;
	}
	/**
	 * @return mediumMouConv
	 */
	public function getMediumMouConv()
	{
		return $this->mediumMouConv;
	}
}