<?php
namespace ERP\Core\Products\Properties;

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
	 * @param String $jfId
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