<?php
namespace ERP\Core\Settings\Professions\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProfessionNamePropertyTrait
{
	/**
     * @var professionName
     */
    private $professionName;
	/**
	 * @param string $professionName
	 */
	public function setProfessionName($professionName)
	{
		$this->professionName = $professionName;
	}
	/**
	 * @return professionName
	 */
	public function getProfessionName()
	{
		return $this->professionName;
	}
}