<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait OutstandingLimitTrait
{
	/**
     * @var outstandingLimit
     */
    private $outstandingLimit;
	/**
	 * @param float $outstandingLimit
	 */
	public function setOutstandingLimit($outstandingLimit)
	{
		$this->outstandingLimit = $outstandingLimit;
	}
	/**
	 * @return outstandingLimit
	 */
	public function getOutstandingLimit()
	{
		return $this->outstandingLimit;
	}
}