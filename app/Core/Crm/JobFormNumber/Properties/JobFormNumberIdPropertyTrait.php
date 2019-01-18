<?php
namespace ERP\Core\Crm\JobFormNumber\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JobFormNumberIdPropertyTrait
{
	/**
     * @var jobCardNumberId
     */
    private $jobCardNumberId;
	/**
	 * @param int $jobCardNumberId
	 */
	public function setJobCardNumberId($jobCardNumberId)
	{
		$this->jobCardNumberId = $jobCardNumberId;
	}
	/**
	 * @return jobCardNumberId
	 */
	public function getJobCardNumberId()
	{
		return $this->jobCardNumberId;
	}
}