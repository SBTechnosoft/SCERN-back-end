<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait DeliveryDatePropertyTrait
{
	/**
     * @var deliveryDate
     */
    private $deliveryDate;
	/**
	 * @param int $deliveryDate
	 */
	public function setDeliveryDate($deliveryDate)
	{
		$this->deliveryDate = $deliveryDate;
	}
	/**
	 * @return deliveryDate
	 */
	public function getDeliveryDate()
	{
		return $this->deliveryDate;
	}
}