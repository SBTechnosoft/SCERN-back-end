<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait UpdatedByTrait
{
	/**
     * @var updatedBy
     */
    private $updatedBy;
	/**
	 * @param float $updatedBy
	 */
	public function setUpdatedBy($updatedBy)
	{
		$this->updatedBy = $updatedBy;
	}
	/**
	 * @return updatedBy
	 */
	public function getUpdatedBy()
	{
		return $this->updatedBy;
	}
}