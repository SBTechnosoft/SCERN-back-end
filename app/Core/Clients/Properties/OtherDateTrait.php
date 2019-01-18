<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait OtherDateTrait
{
	/**
     * @var otherDate
     */
    private $otherDate;
	/**
	 * @param string $otherDate
	 */
	public function setOtherDate($otherDate)
	{
		$this->otherDate = $otherDate;
	}
	/**
	 * @return otherDate
	 */
	public function getOtherDate()
	{
		return $this->otherDate;
	}
}