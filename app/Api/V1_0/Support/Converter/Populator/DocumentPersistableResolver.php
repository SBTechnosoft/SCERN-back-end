<?php
namespace ERP\Api\V1_0\Support\Converter\Populator;

use ERP\Core\Sample\Persistables\DocumentPersistable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class DocumentPersistableResolver 
{
	/**
     *
     * @param DocumentPersistable $persistable
     * @param UploadedFile $file
     * @return DocumentPersistable
     */
    public static function populate(DocumentPersistable $persistable, UploadedFile $file)
    {
        $persistable->setLocation($file->getPathname());
        $persistable->setSuggestedName($file->getClientOriginalName());
        return $persistable;
    }
}