<?php
namespace ERP\Core\Settings\InvoiceNumbers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait StartAtPropertyTrait
{
	/**
     * @var startAt
     */
    private $startAt;
	/**
	 * @param int $startAt
	 */
	public function setStartAt($startAt)
	{
		$this->startAt = $startAt;
	}
	/**
	 * @return startAt
	 */
	public function getStartAt()
	{
		return $this->startAt;
	}
}