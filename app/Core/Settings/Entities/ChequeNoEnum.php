<?php
namespace ERP\Core\Settings\Entities;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ChequeNoEnum 
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['chequeNoEnable'] = "enable";
		$enumArray['chequeNoDisable'] = "disable";
		return $enumArray;
	}
}