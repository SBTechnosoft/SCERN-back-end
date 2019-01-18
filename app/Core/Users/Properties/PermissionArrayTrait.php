<?php
namespace ERP\Core\Users\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PermissionArrayTrait
{
	/**
     * @var permissionArray
     */
    private $permissionArray;
	/**
	 * @param string $permissionArray
	 */
	public function setPermissionArray($permissionArray)
	{
		$this->permissionArray = $permissionArray;
	}
	/**
	 * @return permissionArray
	 */
	public function getPermissionArray()
	{
		return $this->permissionArray;
	}
}