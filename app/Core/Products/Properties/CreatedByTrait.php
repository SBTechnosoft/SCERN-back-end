<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait CreatedByTrait
{
	/**
     * @var createdBy
     */
    private $createdBy;
	/**
	 * @param float $createdBy
	 */
	public function setCreatedBy($createdBy)
	{
		$this->createdBy = $createdBy;
	}
	/**
	 * @return createdBy
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}
}