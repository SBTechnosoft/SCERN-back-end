<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait micrCodeTrait
{
	/**
     * @var micrCode
     */
    private $micrCode;
	/**
	 * @param string $micrCode
	 */
	public function setMicrCode($micrCode)
	{
		$this->micrCode = $micrCode;
	}
	/**
	 * @return micrCode
	 */
	public function getMicrCode()
	{
		return $this->micrCode;
	}
}