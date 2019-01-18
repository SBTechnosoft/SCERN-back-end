<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait KeyArrayPropertyTrait
{
	/**
     * @var keyName
     */
    private $keyName;
	/**
	 * @param string $keyName
	 */
	public function setKey($keyName)
	{
		$this->keyName = $keyName;
	}
	/**
	 * @return key
	 */
	public function getKey()
	{
		return $this->keyName;
	}
}