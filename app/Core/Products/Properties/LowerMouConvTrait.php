<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait LowerMouConvTrait
{
	/**
     * @var lowerMouConv
     */
    private $lowerMouConv;
	/**
	 * @param Integer $lowerMouConv
	 */
	public function setLowerMouConv($lowerMouConv)
	{
		$this->lowerMouConv = $lowerMouConv;
	}
	/**
	 * @return lowerMouConv
	 */
	public function getLowerMouConv()
	{
		return $this->lowerMouConv;
	}
}