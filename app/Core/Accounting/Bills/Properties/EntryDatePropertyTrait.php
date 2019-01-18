<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait EntryDatePropertyTrait
{
	/**
     * @var entryDate
     */
    private $entryDate;
	/**
	 * @param date $entryDate
	 */
	public function setEntryDate($entryDate)
	{
		$this->entryDate = $entryDate;
	}
	/**
	 * @return entryDate
	 */
	public function getEntryDate()
	{
		return $this->entryDate;
	}
}