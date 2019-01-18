<?php
namespace ERP\Debug\Processors;

use Doctrine\ORM\EntityManagerInterface;
use ERP\Api\Support\BaseProcessor;
use ERP\Core\Appraiser\Entities\Appraiser;
use ERP\Core\Customer\Entities\Customer;
use ERP\Core\Customer\Services\CustomerService;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LinkProcessor extends BaseProcessor
{
	protected function rules(Binder $binder)
	{
		echo "rules";
		/**
		 * @var EntityManagerInterface $em
		 */
		$em = $this->container->make(EntityManagerInterface::class);

		$binder->bind('customer', function(Property $property) use ($em){
			$property
				->addRule(new Obligate())
				->addRule(new IntegerCast())
				->addRule((new Callback(function($value) use ($em){
					return (bool) $em->find(Customer::class, $value);
				}))->setIdentifier('exists')->setMessage('Customer does not exist.'));
		});

		$binder->bind('appraiser', function(Property $property) use ($em){
			$property
				->addRule(new Obligate())
				->addRule(new IntegerCast())
				->addRule((new Callback(function($value) use ($em){
					return (bool) $em->find(Appraiser::class, $value);
				}))->setIdentifier('exists')->setMessage('Appraiser does not exist.'));
		});

		$binder->bind('customer', ['customer', 'appraiser'], function(Property $property){
			$property->addRule((new Callback(function(Value $value){

				list($customer, $appraiser) = $value->extract();

				/**
				 * @var CustomerService $customerService
				 */
				$customerService = $this->container->make(CustomerService::class);

				return !$customerService->isRelatedWithAppraiser($customer, $appraiser);
				}))->setIdentifier('link')->setMessage('The customer and the appraiser are already linked.'));
		});
	}

	/**
	 * @return array
	 */
	protected function allowable()
	{
		return [
			'customer', 'appraiser'
		];
	}

	/**
	 * @return int
	 */
	public function getCustomer()
	{
		return $this->get('customer');
	}

	/**
	 * @return int
	 */
	public function getAppraiser()
	{
		return $this->get('appraiser');
	}
}