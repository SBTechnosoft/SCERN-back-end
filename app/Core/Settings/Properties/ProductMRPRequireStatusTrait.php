<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductMrpRequireStatusTrait
{
	/**
     * @var MrpRequire
     */
    private $MrpRequire;
	/**
	 * @param string $MrpRequire
	 */
	public function setProductMrpRequireStatus($MrpRequire)
	{
		$this->MrpRequire = $MrpRequire;
	}
	/**
	 * @return MrpRequire
	 */
	public function getProductMrpRequireStatus()
	{
		return $this->MrpRequire;
	}
}