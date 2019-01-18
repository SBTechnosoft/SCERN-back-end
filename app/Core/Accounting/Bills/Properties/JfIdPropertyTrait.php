<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JfIdPropertyTrait
{
	/**
     * @var jfId
     */
    private $jfId;
	/**
	 * @param float $jfId
	 */
	public function setJfId($jfId)
	{
		$this->jfId = $jfId;
	}
	/**
	 * @return jfId
	 */
	public function getJfId()
	{
		return $this->jfId;
	}
}