<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PoNumberTrait
{
	/**
     * @var poNumber
     */
    private $poNumber;
	/**
	 * @param string $poNumber
	 */
	public function setPoNumber($poNumber)
	{
		$this->poNumber = $poNumber;
	}
	/**
	 * @return poNumber
	 */
	public function getPoNumber()
	{
		return $this->poNumber;
	}
}