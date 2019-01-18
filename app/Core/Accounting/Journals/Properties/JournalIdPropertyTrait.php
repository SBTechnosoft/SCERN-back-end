<?php
namespace ERP\Core\Accounting\Journals\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait JournalIdPropertyTrait
{
	/**
     * @var $journalId
     */
    private $journalId;
	
	/**
	 * @param int $journalId
	 */
	public function setJournalId($journalId)
	{
		$this->journalId = $journalId;
	}
	/**
	 * @return journalId
	 */
	public function getJournalId()
	{
		return $this->journalId;
	}
}