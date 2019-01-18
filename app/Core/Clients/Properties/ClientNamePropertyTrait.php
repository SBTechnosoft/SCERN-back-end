<?php
namespace ERP\Core\Clients\Properties;

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
	 * @param string $clientName
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