<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductBestBeforeStatusTrait
{
	/**
     * @var bestBefore
     */
    private $bestBefore;
	/**
	 * @param string $bestBefore
	 */
	public function setProductBestBeforeStatus($bestBefore)
	{
		$this->bestBefore = $bestBefore;
	}
	/**
	 * @return bestBefore
	 */
	public function getProductBestBeforeStatus()
	{
		return $this->bestBefore;
	}
}