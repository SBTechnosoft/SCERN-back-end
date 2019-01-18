<?php
namespace ERP\Core\Accounting\Journals\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JfIdPropertyTrait
{
	/**
     * @var $jfId
     */
    private $jfId;
	
	/**
	 * @param int $jfId
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