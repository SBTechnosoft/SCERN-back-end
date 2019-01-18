<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PanPropertyTrait
{
	/**
     * @var pan
     */
    private $pan;
	/**
	 * @param int $pan
	 */
	public function setPan($pan)
	{
		$this->pan = $pan;
	}
	/**
	 * @return pan
	 */
	public function getPan()
	{
		return $this->pan;
	}
}