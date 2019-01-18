<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductAdvanceMouStatusTrait
{
	/**
     * @var advanceMou
     */
    private $advanceMou;
	/**
	 * @param string $advanceMou
	 */
	public function setProductAdvanceMouStatus($advanceMou)
	{
		$this->advanceMou = $advanceMou;
	}
	/**
	 * @return advanceMou
	 */
	public function getProductAdvanceMouStatus()
	{
		return $this->advanceMou;
	}
}