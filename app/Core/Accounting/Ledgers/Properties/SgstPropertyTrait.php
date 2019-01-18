<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait SgstPropertyTrait
{
	/**
     * @var sgst
     */
    private $sgst;
	/**
	 * @param int $sgst
	 */
	public function setSgst($sgst)
	{
		$this->sgst = $sgst;
	}
	/**
	 * @return sgst
	 */
	public function getSgst()
	{
		return $this->sgst;
	}
}