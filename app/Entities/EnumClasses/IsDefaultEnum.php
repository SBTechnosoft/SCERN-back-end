<?php
namespace ERP\Entities\EnumClasses;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class IsDefaultEnum 
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['default'] = "ok";
		$enumArray['notDefault'] = "not";
		return $enumArray;
	}
}
