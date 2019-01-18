<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JobCardNumberPropertyTrait
{
	/**
     * @var jobCardNumber
     */
    private $jobCardNumber;
	/**
	 * @param string $jobCardNumber
	 */
	public function setJobCardNumber($jobCardNumber)
	{
		$this->jobCardNumber = $jobCardNumber;
	}
	/**
	 * @return jobCardNumber
	 */
	public function getJobCardNumber()
	{
		return $this->jobCardNumber;
	}
}