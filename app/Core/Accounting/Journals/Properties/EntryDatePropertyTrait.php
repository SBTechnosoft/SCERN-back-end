<?php
namespace ERP\Core\Accounting\Journals\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait EntryDatePropertyTrait
{
	/**
     * @var $entryDate
     */
    public $entryDate;
	
	/**
	 * @param int $entryDate
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