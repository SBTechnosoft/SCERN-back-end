<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait LanguageSettingTypeTrait
{
	/**
     * @var languageSettingType
     */
    private $languageSettingType;
	/**
	 * @param string $languageSettingType
	 */
	public function setLanguageSettingType($languageSettingType)
	{
		$this->languageSettingType = $languageSettingType;
	}
	/**
	 * @return languageSettingType
	 */
	public function getLanguageSettingType()
	{
		return $this->languageSettingType;
	}
}