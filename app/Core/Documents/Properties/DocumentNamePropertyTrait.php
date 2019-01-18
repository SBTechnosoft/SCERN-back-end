<?php
namespace ERP\Core\Documents\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait DocumentNamePropertyTrait
{
	/**
     * @var documentName
     */
    private $documentName;
	/**
	 * @param int $documentName
	 */
	public function setDocumentName($documentName)
	{
		$this->documentName = $documentName;
	}
	/**
	 * @return documentName
	 */
	public function getDocumentName()
	{
		return $this->documentName;
	}
}