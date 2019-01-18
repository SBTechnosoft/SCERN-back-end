<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait TotalSgstPercentageTrait
{
	/**
     * @var totalSgstPercentage
     */
    private $totalSgstPercentage;
	/**
	 * @param float $totalSgstPercentage
	 */
	public function setTotalSgstPercentage($totalSgstPercentage)
	{
		$this->totalSgstPercentage = $totalSgstPercentage;
	}
	/**
	 * @return totalSgstPercentage
	 */
	public function getTotalSgstPercentage()
	{
		return $this->totalSgstPercentage;
	}
}