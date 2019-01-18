<?php
namespace ERP\Core\Crm\JobForm\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ServiceTypeEnum
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['paidType'] = "paid";
		$enumArray['freeType'] = "free";
		return $enumArray;
	}
}