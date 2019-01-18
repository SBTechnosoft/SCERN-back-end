<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait NotForSaleTrait
{
	/**
     * @var notForSale
     */
    private $notForSale;
	/**
	 * @param float $notForSale
	 */
	public function setNotForSale($notForSale)
	{
		$this->notForSale = $notForSale;
	}
	/**
	 * @return notForSale
	 */
	public function getNotForSale()
	{
		return $this->notForSale;
	}
}