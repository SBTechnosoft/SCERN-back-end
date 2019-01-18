<?php
namespace ERP\Core\Settings\Professions\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait DescriptionPropertyTrait
{
	/**
     * @var description
     */
    private $description;
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	/**
	 * @return description
	 */
	public function getDescription()
	{
		return $this->description;
	}
}