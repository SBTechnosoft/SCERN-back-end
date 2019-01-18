<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductMarginStatusTrait
{
	/**
     * @var marginEnable
     */
    private $marginEnable;
	/**
	 * @param string $marginEnable
	 */
	public function setProductMarginStatus($marginEnable)
	{
		$this->marginEnable = $marginEnable;
	}
	/**
	 * @return marginEnable
	 */
	public function getProductMarginStatus()
	{
		return $this->marginEnable;
	}
}