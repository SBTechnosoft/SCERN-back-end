<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait WorkFlowQuotationStatusTrait
{
	/**
     * @var workFlowQuotationStatus
     */
    private $workFlowQuotationStatus;
	/**
	 * @param string $workFlowQuotationStatus
	 */
	public function setWorkFlowQuotationStatus($workFlowQuotationStatus)
	{
		$this->workFlowQuotationStatus = $workFlowQuotationStatus;
	}
	/**
	 * @return workFlowQuotationStatus
	 */
	public function getWorkFlowQuotationStatus()
	{
		return $this->workFlowQuotationStatus;
	}
}