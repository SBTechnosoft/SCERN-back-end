<?php
namespace ERP\Core\Settings\Templates\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TemplateTypePropertyTrait
{
	/**
     * @var templateType
     */
    private $templateType;
	/**
	 * @param int $templateType
	 */
	public function setTemplateType($templateType)
	{
		$this->templateType = $templateType;
	}
	/**
	 * @return templateType
	 */
	public function getTemplateType()
	{
		return $this->templateType;
	}
}