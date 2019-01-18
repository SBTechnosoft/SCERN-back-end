<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ClientIdPropertyTrait
{
	/**
     * @var clientId
     */
    private $clientId;
	/**
	 * @param int $clientId
	 */
	public function setClientId($clientId)
	{
		$this->clientId = $clientId;
	}
	/**
	 * @return clientId
	 */
	public function getClientId()
	{
		return $this->clientId;
	}
}