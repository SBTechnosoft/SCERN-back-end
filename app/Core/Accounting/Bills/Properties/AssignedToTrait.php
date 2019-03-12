<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait AssignedToTrait
{
	/**
     * @var assignedTo
     */
    private $assignedTo;
	/**
	 * @param float $assignedTo
	 */
	public function setAssignedTo($assignedTo)
	{
		$this->assignedTo = $assignedTo;
	}
	/**
	 * @return assignedTo
	 */
	public function getAssignedTo()
	{
		return $this->assignedTo;
	}
}