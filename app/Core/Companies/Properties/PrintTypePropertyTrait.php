<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PrintTypePropertyTrait
{
	/**
     * @var printType
     */
    private $printType;
	/**
	 * @param string $printType
	 */
	public function setPrintType($printType)
	{
		$this->printType = $printType;
	}
	/**
	 * @return printType
	 */
	public function getPrintType()
	{
		return $this->printType;
	}
}