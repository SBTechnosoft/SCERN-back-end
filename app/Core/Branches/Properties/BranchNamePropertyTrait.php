<?php
namespace ERP\Core\Branches\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BranchNamePropertyTrait
{
	/**
     * @var branchName
     */
    private $branchName;
	/**
	 * @param int $branchName
	 */
	public function setBranchName($branchName)
	{
		$this->branchName = $branchName;
	}
	/**
	 * @return branchName
	 */
	public function getBranchName()
	{
		return $this->branchName;
	}
}