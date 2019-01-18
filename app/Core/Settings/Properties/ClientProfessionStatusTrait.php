<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ClientProfessionStatusTrait
{
	/**
     * @var profession
     */
    private $profession;
	/**
	 * @param string $profession
	 */
	public function setClientProfessionStatus($profession)
	{
		$this->profession = $profession;
	}
	/**
	 * @return profession
	 */
	public function getClientProfessionStatus()
	{
		return $this->profession;
	}
}