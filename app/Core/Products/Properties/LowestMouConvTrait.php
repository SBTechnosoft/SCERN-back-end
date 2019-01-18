<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait LowestMouConvTrait
{
	/**
     * @var lowestMouConv
     */
    private $lowestMouConv;
	/**
	 * @param Integer $lowestMouConv
	 */
	public function setLowestMouConv($lowestMouConv)
	{
		$this->lowestMouConv = $lowestMouConv;
	}
	/**
	 * @return lowestMouConv
	 */
	public function getLowestMouConv()
	{
		return $this->lowestMouConv;
	}
}