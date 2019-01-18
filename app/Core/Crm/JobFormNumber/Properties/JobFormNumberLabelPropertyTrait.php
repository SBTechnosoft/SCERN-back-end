<?php
namespace ERP\Core\Crm\JobFormNumber\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JobFormNumberLabelPropertyTrait
{
	/**
     * @var jobCardNumberLabel
     */
    private $jobCardNumberLabel;
	/**
	 * @param String $jobCardNumberLabel
	 */
	public function setJobCardNumberLabel($jobCardNumberLabel)
	{
		$this->jobCardNumberLabel = $jobCardNumberLabel;
	}
	/**
	 * @return jobCardNumberLabel
	 */
	public function getJobCardNumberLabel()
	{
		return $this->jobCardNumberLabel;
	}
}