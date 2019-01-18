<?php
namespace ERP\Core\Companies\Entities;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PrintEnum 
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['print'] = "print";
		$enumArray['preprint'] = "preprint";
		return $enumArray;
	}
}
