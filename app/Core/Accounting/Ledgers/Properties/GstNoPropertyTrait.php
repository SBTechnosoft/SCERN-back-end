<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait GstNoPropertyTrait
{
	/**
     * @var gstNo
     */
    private $gstNo;
	/**
	 * @param int $gstNo
	 */
	public function setGst($gstNo)
	{
		$this->gstNo = $gstNo;
	}
	/**
	 * @return gstNo
	 */
	public function getGst()
	{
		return $this->gstNo;
	}
}