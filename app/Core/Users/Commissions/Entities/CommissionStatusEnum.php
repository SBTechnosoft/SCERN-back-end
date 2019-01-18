<?php
namespace ERP\Core\Users\Commissions\Entities;
/**
 *
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CommissionStatusEnum
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['enableStatus'] = "on";
		$enumArray['disableStatus'] = "off";
		return $enumArray;
	}
}