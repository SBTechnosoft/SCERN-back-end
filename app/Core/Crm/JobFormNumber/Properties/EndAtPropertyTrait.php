<?php
namespace ERP\Core\Crm\JobFormNumber\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait EndAtPropertyTrait
{
	/**
     * @var endAt
     */
    private $endAt;
	/**
	 * @param int $endAt
	 */
	public function setEndAt($endAt)
	{
		$this->endAt = $endAt;
	}
	/**
	 * @return endAt
	 */
	public function getEndAt()
	{
		return $this->endAt;
	}
}