<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CheckNumberPropertyTrait
{
	/**
     * @var checkNumber
     */
    private $checkNumber;
	/**
	 * @param string $checkNumber
	 */
	public function setCheckNumber($checkNumber)
	{
		$this->checkNumber = $checkNumber;
	}
	/**
	 * @return checkNumber
	 */
	public function getCheckNumber()
	{
		return $this->checkNumber;
	}
}