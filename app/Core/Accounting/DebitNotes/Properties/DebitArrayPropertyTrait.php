<?php
namespace ERP\Core\Accounting\DebitNotes\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait DebitArrayPropertyTrait
{
	/**
     * @var debitNoteArray
     */
    private $debitNoteArray;
	/**
	 * @param float $debitNoteArray
	 */
	public function setDebitArray($debitNoteArray)
	{
		$this->debitNoteArray = $debitNoteArray;
	}
	/**
	 * @return debitNoteArray
	 */
	public function getDebitArray()
	{
		return $this->debitNoteArray;
	}
}