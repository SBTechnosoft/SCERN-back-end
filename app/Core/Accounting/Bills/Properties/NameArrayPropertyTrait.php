<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait NameArrayPropertyTrait
{
	/**
     * @var name
     */
    private $name;
	/**
	 * @param float $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	/**
	 * @return name
	 */
	public function getName()
	{
		return $this->name;
	}
}