<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AdvancePropertyTrait
{
	/**
     * @var advance
     */
    private $advance;
	/**
	 * @param float $advance
	 */
	public function setAdvance($advance)
	{
		$this->advance = $advance;
	}
	/**
	 * @return advance
	 */
	public function getAdvance()
	{
		return $this->advance;
	}
}