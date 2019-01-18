<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProfessionIdTrait
{
	/**
     * @var professionId
     */
    private $professionId;
	/**
	 * @param float $professionId
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