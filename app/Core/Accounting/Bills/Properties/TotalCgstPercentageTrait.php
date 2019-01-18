<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait TotalCgstPercentageTrait
{
	/**
     * @var totalCgstPercentage
     */
    private $totalCgstPercentage;
	/**
	 * @param float $totalCgstPercentage
	 */
	public function setTotalCgstPercentage($totalCgstPercentage)
	{
		$this->totalCgstPercentage = $totalCgstPercentage;
	}
	/**
	 * @return totalCgstPercentage
	 */
	public function getTotalCgstPercentage()
	{
		return $this->totalCgstPercentage;
	}
}