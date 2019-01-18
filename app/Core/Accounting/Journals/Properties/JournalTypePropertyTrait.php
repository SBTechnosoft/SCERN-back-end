<?php
namespace ERP\Core\Accounting\Journals\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JournalTypePropertyTrait
{
	/**
     * @var $journalType
     */
    private $journalType;
	
	/**
	 * @param int $journalType
	 */
	public function setJournalType($journalType)
	{
		$this->journalType = $journalType;
	}
	/**
	 * @return journalType
	 */
	public function getJournalType()
	{
		return $this->journalType;
	}
}