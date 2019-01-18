<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CgstPropertyTrait
{
	/**
     * @var cgst
     */
    private $cgst;
	/**
	 * @param int $cgst
	 */
	public function setCgst($cgst)
	{
		$this->cgst = $cgst;
	}
	/**
	 * @return cgst
	 */
	public function getCgst()
	{
		return $this->cgst;
	}
}