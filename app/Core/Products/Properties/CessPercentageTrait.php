<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CessPercentageTrait
{
	/**
     * @var cessPercentage
     */
    private $cessPercentage;
	/**
	 * @param float $cessPercentage
	 */
	public function setCessPercentage($cessPercentage)
	{
		$this->cessPercentage = $cessPercentage;
	}
	/**
	 * @return cessPercentage
	 */
	public function getCessPercentage()
	{
		return $this->cessPercentage;
	}
}