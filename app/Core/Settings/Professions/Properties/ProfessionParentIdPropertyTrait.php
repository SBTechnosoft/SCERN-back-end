<?php
namespace ERP\Core\Settings\Professions\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProfessionParentIdPropertyTrait
{
	/**
     * @var professionParentId
     */
    private $professionParentId;
	/**
	 * @param int $professionParentId
	 */
	public function setProfessionParentId($professionParentId)
	{
		$this->professionParentId = $professionParentId;
	}
	/**
	 * @return professionParentId
	 */
	public function getProfessionParentId()
	{
		return $this->professionParentId;
	}
}