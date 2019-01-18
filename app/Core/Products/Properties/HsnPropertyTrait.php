<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait HsnPropertyTrait
{
	/**
     * @var hsn
     */
    private $hsn;
	/**
	 * @param float $hsn
	 */
	public function setHsn($hsn)
	{
		$this->hsn = $hsn;
	}
	/**
	 * @return hsn
	 */
	public function getHsn()
	{
		return $this->hsn;
	}
}