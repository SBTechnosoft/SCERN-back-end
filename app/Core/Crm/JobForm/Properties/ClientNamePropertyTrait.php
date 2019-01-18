<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ClientNamePropertyTrait
{
	/**
     * @var clientName
     */
    private $clientName;
	/**
	 * @param int $clientName
	 */
	public function setClientName($clientName)
	{
		$this->clientName = $clientName;
	}
	/**
	 * @return clientName
	 */
	public function getClientName()
	{
		return $this->clientName;
	}
}