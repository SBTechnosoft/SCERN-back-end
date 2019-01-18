<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TransactionTypePropertyTrait
{
	/**
     * @var tranType
     */
    private $tranType;
	/**
	 * @param String $tranType
	 */
	public function setTransactionType($tranType)
	{
		$this->tranType = $tranType;
	}
	/**
	 * @return tranType
	 */
	public function getTransactionType()
	{
		return $this->tranType;
	}
}