<?php
namespace ERP\Entities\EnumClasses;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class IsDisplayEnum 
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['display'] = "yes";
		$enumArray['notDisplay'] = "no";
		return $enumArray;
	}
}
