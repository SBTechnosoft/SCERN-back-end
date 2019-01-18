<?php
namespace ERP\Core\Branches\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BranchIdPropertyTrait
{
	/**
     * @var branchId
     */
    private $branchId;
	/**
	 * @param int $branchId
	 */
	public function setBranchId($branchId)
	{
		$this->branchId = $branchId;
	}
	/**
	 * @return branchId
	 */
	public function getBranchId()
	{
		return $this->branchId;
	}
}