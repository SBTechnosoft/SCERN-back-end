<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait WebsiteNameTrait
{
	/**
     * @var websiteName
     */
    private $websiteName;
	/**
	 * @param string $websiteName
	 */
	public function setWebsiteName($websiteName)
	{
		$this->websiteName = $websiteName;
	}
	/**
	 * @return websiteName
	 */
	public function getWebsiteName()
	{
		return $this->websiteName;
	}
}