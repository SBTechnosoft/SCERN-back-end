<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait IsDealerPropertyTrait
{
	/**
     * @var isDealer
     */
    private $isDealer;
	/**
	 * @param int $isDealer
	 */
	public function setIsDealer($isDealer)
	{
		$this->isDealer = $isDealer;
	}
	/**
	 * @return isDealer
	 */
	public function getIsDealer()
	{
		return $this->isDealer;
	}
}