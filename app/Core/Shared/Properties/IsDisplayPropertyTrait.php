<?php
namespace ERP\Core\Shared\Properties;

/**
 * @author Igor Vorobiov <igor.vorobioff@gmail.com>
 */
trait IsDisplayPropertyTrait
{
	/**
	 * @var string
	 */
	private $isDisplay;

	/**
	 * @param string $isDisplay
	 */
	public function setIsDisplay($isDisplay)
	{
		$this->isDisplay = $isDisplay;
	}

	/**
	 * @return string isDisplay
	 */
	public function getIsDisplay()
	{
		return $this->isDisplay;
	}
}