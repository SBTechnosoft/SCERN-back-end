<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JobCardNoPropertyTrait
{
	/**
     * @var jobCardNo
     */
    private $jobCardNo;
	/**
	 * @param int $jobCardNo
	 */
	public function setJobCardNo($jobCardNo)
	{
		$this->jobCardNo = $jobCardNo;
	}
	/**
	 * @return jobCardNo
	 */
	public function getJobCardNo()
	{
		return $this->jobCardNo;
	}
}