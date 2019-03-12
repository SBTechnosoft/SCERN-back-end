<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait AssignedByTrait
{
	/**
     * @var assignedBy
     */
    private $assignedBy;
	/**
	 * @param float $assignedBy
	 */
	public function setAssignedBy($assignedBy)
	{
		$this->assignedBy = $assignedBy;
	}
	/**
	 * @return assignedBy
	 */
	public function getAssignedBy()
	{
		return $this->assignedBy;
	}
}