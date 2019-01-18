<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BirthDateTrait
{
	/**
     * @var birthDate
     */
    private $birthDate;
	/**
	 * @param string $birthDate
	 */
	public function setBirthDate($birthDate)
	{
		$this->birthDate = $birthDate;
	}
	/**
	 * @return birthDate
	 */
	public function getBirthDate()
	{
		return $this->birthDate;
	}
}