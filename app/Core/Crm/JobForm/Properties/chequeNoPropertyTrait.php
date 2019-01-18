<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait chequeNoPropertyTrait
{
	/**
     * @var chequeNo
     */
    private $chequeNo;
	/**
	 * @param int $chequeNo
	 */
	public function setChequeNo($chequeNo)
	{
		$this->chequeNo = $chequeNo;
	}
	/**
	 * @return chequeNo
	 */
	public function getChequeNo()
	{
		return $this->chequeNo;
	}
}