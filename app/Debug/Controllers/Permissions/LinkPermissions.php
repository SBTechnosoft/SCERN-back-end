<?php
namespace ValuePad\Debug\Controllers\Permissions;

use Ascope\Libraries\Permissions\AbstractActionsPermissions;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LinkPermissions extends AbstractActionsPermissions
{
	/**
	 * @return array
	 */
	protected function permissions()
	{
		return [
			'store' => 'all'
		];
	}
}