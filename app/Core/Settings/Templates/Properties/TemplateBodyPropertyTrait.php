<?php
namespace ERP\Core\Settings\Templates\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TemplateBodyPropertyTrait
{
	/**
     * @var templateBody
     */
    private $templateBody;
	/**
	 * @param int $templateBody
	 */
	public function setTemplateBody($templateBody)
	{
		$this->templateBody = $templateBody;
	}
	/**
	 * @return templateBody
	 */
	public function getTemplateBody()
	{
		return $this->templateBody;
	}
}