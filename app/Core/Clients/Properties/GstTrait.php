<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait GstTrait
{
	/**
     * @var gst
     */
    private $gst;
	/**
	 * @param string $gst
	 */
	public function setGst($gst)
	{
		$this->gst = $gst;
	}
	/**
	 * @return gst
	 */
	public function getGst()
	{
		return $this->gst;
	}
}