<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait VatNoPropertyTrait
{
	/**
     * @var vatNo
     */
    private $vatNo;
	/**
	 * @param int $vatNo
	 */
	public function setVatNo($vatNo)
	{
		$this->vatNo = $vatNo;
	}
	/**
	 * @return vatNo
	 */
	public function getVatNo()
	{
		return $this->vatNo;
	}
}