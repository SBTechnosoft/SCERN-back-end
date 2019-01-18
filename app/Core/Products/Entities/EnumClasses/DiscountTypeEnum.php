<?php
namespace ERP\Core\Products\Entities\EnumClasses;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class DiscountTypeEnum 
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['flatType'] = "flat";
		$enumArray['percentageType'] = "percentage";
		return $enumArray;
	}
}