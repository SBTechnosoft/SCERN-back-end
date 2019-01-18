<?php
namespace ERP\Core\Settings\Professions\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProfessionIdPropertyTrait
{
	/**
     * @var professionId
     */
    private $professionId;
	/**
	 * @param int $professionId
	 */
	public function setProfessionId($professionId)
	{
		$this->professionId = $professionId;
	}
	/**
	 * @return professionId
	 */
	public function getProfessionId()
	{
		return $this->professionId;
	}
}