<?php
namespace ERP\Core\Settings\Templates\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TemplateIdPropertyTrait
{
	/**
     * @var templateId
     */
    private $templateId;
	/**
	 * @param int $templateId
	 */
	public function setTemplateId($templateId)
	{
		$this->templateId = $templateId;
	}
	/**
	 * @return templateId
	 */
	public function getTemplateId()
	{
		return $this->templateId;
	}
}