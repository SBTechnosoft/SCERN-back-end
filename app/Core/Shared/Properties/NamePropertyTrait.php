<?php
namespace ERP\Core\Shared\Properties;

/**
 * @author Igor Vorobiov <igor.vorobioff@gmail.com>
 */
trait NamePropertyTrait
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}