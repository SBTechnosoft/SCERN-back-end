<?php
namespace ERP\Core\Documents\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait DocumentSizePropertyTrait
{
	/**
     * @var documentSize
     */
    private $documentSize;
	/**
	 * @param int $documentSize
	 */
	public function setDocumentSize($documentSize)
	{
		$this->documentSize = $documentSize;
	}
	/**
	 * @return documentSize
	 */
	public function getDocumentSize()
	{
		return $this->documentSize;
	}
}