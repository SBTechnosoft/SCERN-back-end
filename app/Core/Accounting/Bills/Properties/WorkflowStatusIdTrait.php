<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait WorkflowStatusIdTrait
{
	/**
     * @var workflowStatusId
     */
    private $workflowStatusId;
	/**
	 * @param float $workflowStatusId
	 */
	public function setWorkflowStatusId($workflowStatusId)
	{
		$this->workflowStatusId = $workflowStatusId;
	}
	/**
	 * @return workflowStatusId
	 */
	public function getWorkflowStatusId()
	{
		return $this->workflowStatusId;
	}
}