<?php
namespace ERP\Core\Products\Entities\EnumClasses;
/**
 *
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class PrimaryMeasureUnitEnum
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['highestType'] = "highest";
		$enumArray['higherType'] = "higher";
		$enumArray['mediumType'] = "medium";
		$enumArray['mediumLowerType'] = "mediumLower";
		$enumArray['lowerType'] = "lower";
		$enumArray['lowestType'] = "lowest";
		return $enumArray;
	}
}