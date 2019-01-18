<?php
namespace ERP\Core\Crm\JobForm\Properties;

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
	 * @param int $advance
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