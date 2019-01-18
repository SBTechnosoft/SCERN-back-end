<?php
namespace ERP\Core\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ConvertCase
{
	public function dashesToCamelCase($string,$capitalizeFirstCharacter = false) 
	{
		$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
		return $str;
	}
    
}