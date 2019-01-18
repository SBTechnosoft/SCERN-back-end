<?php
namespace ERP\Core\Crm\Conversations\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BccEmailIdTrait
{
	/**
     * @var bccEmailId
     */
    private $bccEmailId;
	/**
	 * @param string $bccEmailId
	 */
	public function setBccEmailId($bccEmailId)
	{
		$this->bccEmailId = $bccEmailId;
	}
	/**
	 * @return bccEmailId
	 */
	public function getBccEmailId()
	{
		return $this->bccEmailId;
	}
}