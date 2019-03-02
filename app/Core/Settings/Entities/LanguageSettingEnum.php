<?php
namespace ERP\Core\Settings\Entities;
/**
 *
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class LanguageSettingEnum 
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray[0] = 'english';
		$enumArray[1] = 'hindi';
		return $enumArray;
	}
}