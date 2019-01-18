<?php
namespace ERP\Core\Products\Entities\EnumClasses;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class measurementUnitEnum 
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['type1'] = "piece";
		$enumArray['type2'] = "pair";
		return $enumArray;
	}
}