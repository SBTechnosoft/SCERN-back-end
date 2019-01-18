<?php
namespace ERP\Core\Users\Commissions\Entities;
/**
 *
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CommissionRateTypeEnum
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['flatType'] = "flat";
		$enumArray['percType'] = "percentage";
		return $enumArray;
	}
}