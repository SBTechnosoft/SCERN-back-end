<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductMenuTrait
{
	/**
     * @var productMenu
     */
    private $productMenu;
	/**
	 * @param float $productMenu
	 */
	public function setProductMenu($productMenu)
	{
		$this->productMenu = $productMenu;
	}
	/**
	 * @return productMenu
	 */
	public function getProductMenu()
	{
		return $this->productMenu;
	}
}