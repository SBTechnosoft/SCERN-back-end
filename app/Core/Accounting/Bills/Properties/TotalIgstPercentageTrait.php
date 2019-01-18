<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait TotalIgstPercentageTrait
{
	/**
     * @var totalIgstPercentage
     */
    private $totalIgstPercentage;
	/**
	 * @param float $totalIgstPercentage
	 */
	public function setTotalIgstPercentage($totalIgstPercentage)
	{
		$this->totalIgstPercentage = $totalIgstPercentage;
	}
	/**
	 * @return totalIgstPercentage
	 */
	public function getTotalIgstPercentage()
	{
		return $this->totalIgstPercentage;
	}
}