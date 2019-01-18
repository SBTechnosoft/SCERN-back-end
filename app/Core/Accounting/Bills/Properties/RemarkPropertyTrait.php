<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait RemarkPropertyTrait
{
	/**
     * @var remark
     */
    private $remark;
	/**
	 * @param string $remark
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