<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AliasPropertyTrait
{
	/**
     * @var alias
     */
    private $alias;
	/**
	 * @param int $alias
	 */
	public function setAlias($alias)
	{
		$this->alias = $alias;
	}
	/**
	 * @return alias
	 */
	public function getAlias()
	{
		return $this->alias;
	}
}