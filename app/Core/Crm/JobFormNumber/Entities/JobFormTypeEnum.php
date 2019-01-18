<?php
namespace ERP\Core\Crm\JobFormNumber\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormTypeEnum
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['beforeJobFormNumber'] = "prefix";
		$enumArray['afterJobFormNumber'] = "postfix";
		return $enumArray;
	}
}