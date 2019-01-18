<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CessFlatTrait
{
	/**
     * @var cessFlat
     */
    private $cessFlat;
	/**
	 * @param float $cessFlat
	 */
	public function setCessFlat($cessFlat)
	{
		$this->cessFlat = $cessFlat;
	}
	/**
	 * @return cessFlat
	 */
	public function getCessFlat()
	{
		return $this->cessFlat;
	}
}