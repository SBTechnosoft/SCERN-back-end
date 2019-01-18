<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductFrameNoStatusTrait
{
	/**
     * @var frameNo
     */
    private $frameNo;
	/**
	 * @param string $frameNo
	 */
	public function setProductFrameNoStatus($frameNo)
	{
		$this->frameNo = $frameNo;
	}
	/**
	 * @return frameNo
	 */
	public function getProductFrameNoStatus()
	{
		return $this->frameNo;
	}
}