<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait RemarkTrait
{
	/**
     * @var remark
     */
    private $remark;
	/**
	 * @param float $remark
	 */
	public function setRemark($remark)
	{
		$this->remark = $remark;
	}
	/**
	 * @return remark
	 */
	public function getRemark()
	{
		return $this->remark;
	}
}