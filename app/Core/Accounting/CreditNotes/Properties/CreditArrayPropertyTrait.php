<?php
namespace ERP\Core\Accounting\CreditNotes\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait CreditArrayPropertyTrait
{
	/**
     * @var creditNoteArray
     */
    private $creditNoteArray;
	/**
	 * @param float $creditNoteArray
	 */
	public function setCreditArray($creditNoteArray)
	{
		$this->creditNoteArray = $creditNoteArray;
	}
	/**
	 * @return creditNoteArray
	 */
	public function getCreditArray()
	{
		return $this->creditNoteArray;
	}
}