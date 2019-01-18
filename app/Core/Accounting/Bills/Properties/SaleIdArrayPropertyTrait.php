<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait SaleIdArrayPropertyTrait
{
	/**
     * @var saleId
     */
    private $saleId;
	/**
	 * @param float $saleId
	 */
	public function setSaleId($saleId)
	{
		$this->saleId = $saleId;
	}
	/**
	 * @return saleId
	 */
	public function getSaleId()
	{
		return $this->saleId;
	}
}