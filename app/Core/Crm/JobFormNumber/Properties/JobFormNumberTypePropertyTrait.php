<?php
namespace ERP\Core\Crm\JobFormNumber\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JobFormNumberTypePropertyTrait
{
	/**
     * @var jobCardNumberType
     */
    private $jobCardNumberType;
	/**
	 * @param String $jobCardNumberType
	 */
	public function setJobCardNumberType($jobCardNumberType)
	{
		$this->jobCardNumberType = $jobCardNumberType;
	}
	/**
	 * @return jobCardNumberType
	 */
	public function getJobCardNumberType()
	{
		return $this->jobCardNumberType;
	}
}