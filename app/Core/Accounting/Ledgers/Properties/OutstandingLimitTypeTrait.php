<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait OutstandingLimitTypeTrait
{
	/**
     * @var outstandingLimitType
     */
    private $outstandingLimitType;
	/**
	 * @param string $outstandingLimitType
	 */
	public function setOutstandingLimitType($outstandingLimitType)
	{
		$this->outstandingLimitType = $outstandingLimitType;
	}
	/**
	 * @return outstandingLimitType
	 */
	public function getOutstandingLimitType()
	{
		return $this->outstandingLimitType;
	}
}