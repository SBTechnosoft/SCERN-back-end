<?php
namespace ERP\Core\Settings\Templates\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TemplateNamePropertyTrait
{
	/**
     * @var templateName
     */
    private $templateName;
	/**
	 * @param int $templateName
	 */
	public function setTemplateName($templateName)
	{
		$this->templateName = $templateName;
	}
	/**
	 * @return templateName
	 */
	public function getTemplateName()
	{
		return $this->templateName;
	}
}