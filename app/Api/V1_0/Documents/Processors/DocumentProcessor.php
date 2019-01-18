<?php
namespace ERP\Api\V1_0\Documents\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Documents\Persistables\DocumentPersistable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Entities\Constants\ConstantClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class DocumentProcessor extends BaseProcessor
{
	/**
     * @var documentPersistable
	 * @var request
     */
	private $documentPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Document Persistable object
     */	
    public function createPersistable(Request $request,$documentPath)
	{	
		$documentUrl=array();
		$documentName=array();
		$documentFormat=array();
		$documentSize=array();
		$persistableArray = array();
		
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		
		//change the name of document-name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		//get constant document-url from document
		$constDocumentUrl =  new ConstantClass();
		$documentArray = $constDocumentUrl->constantVariable();
		if(in_array(true,$request->file()))
		{
			if(array_key_exists("file", $request->file()))
			{
				$countDocument = count($request->file()['file']);
				$file = $request->file();
				//get document data and store documents in folder		
				for($fileArray=0;$fileArray<count($request->file()['file']);$fileArray++)
				{
					$documentPersistable = array();
					$documentPersistable[$fileArray] = new DocumentPersistable();
					
					$documentUrl[$fileArray] = $documentPath;
					$documentFormat[$fileArray] = $file['file'][$fileArray]->getClientOriginalExtension();
					$documentName[$fileArray] = $combineDateTime.mt_rand(1,9999).$fileArray.mt_rand(1,9999).".".$documentFormat[$fileArray];
					$documentSize[$fileArray] = $file['file'][$fileArray]->getClientSize();
					$file['file'][$fileArray]->move($documentUrl[$fileArray],$documentName[$fileArray]);
					$documentFormat[$fileArray] = strtolower($documentFormat[$fileArray]);
					if($documentFormat[$fileArray]=='jpg' || $documentFormat[$fileArray]=='jpeg' || $documentFormat[$fileArray]=='gif' || $documentFormat[$fileArray]=='png' || $documentFormat[$fileArray]=='pdf' || $documentFormat[$fileArray]=='bmp')
					{	
						if(($documentSize[$fileArray]/1048576)<=5)
						{
							$documentPersistable[$fileArray]->setDocumentName($documentName[$fileArray]);
							$documentPersistable[$fileArray]->setDocumentSize($documentSize[$fileArray]);
							$documentPersistable[$fileArray]->setDocumentFormat($documentFormat[$fileArray]);
							$documentPersistable[$fileArray]->setDocumentUrl($documentUrl[$fileArray]);
							$persistableArray[$fileArray] = $documentPersistable[$fileArray];
						}
						else
						{
							return $msgArray['fileSize'];
						}
					}
					else
					{
						return $msgArray['415'];
					}
				}
			}
			else
			{
				$countDocument = 0;
			}
		}
		else
		{
			$countDocument = 0;
		}
		if(array_key_exists('scanFile',$request->input()))
		{
			$countScanFile = $request->input()['scanFile'];
		
			for($scanFileArray=0;$scanFileArray<count($countScanFile);$scanFileArray++)
			{
				$totalCount = $countDocument+$scanFileArray;
				$documentPersistable = array();
				$documentPersistable[$scanFileArray] = new DocumentPersistable();
				
				$scanDocumentName[$scanFileArray] = $combineDateTime."s".mt_rand(1,9999).$scanFileArray.mt_rand(1,9999)."c.png";
				$img = str_replace('data:image/png;base64,', '',$request->input()['scanFile'][$scanFileArray]);
				$decodedFile = base64_decode($img);
				
				$openFile = fopen($documentPath.$scanDocumentName[$scanFileArray],'w');
				fwrite($openFile,$decodedFile);
				fclose($openFile);
				$documentPersistable[$scanFileArray]->setDocumentName($scanDocumentName[$scanFileArray]);
				$documentPersistable[$scanFileArray]->setDocumentSize('0');
				$documentPersistable[$scanFileArray]->setDocumentFormat('png');
				$documentPersistable[$scanFileArray]->setDocumentUrl($documentPath);
				$persistableArray[$totalCount] = $documentPersistable[$scanFileArray];
			}
		}
		else if(array_key_exists('coverImage',$request->file()))
		{
			$totalCount = $countDocument+0;
			$coverFile = $request->file()['coverImage'];
			$documentPersistable = array();
			$documentPersistable = new DocumentPersistable();
			$documentUrl = $documentPath."CoverImage/";
			$documentFormat = $coverFile[0]->getClientOriginalExtension();
			$documentName = $combineDateTime.mt_rand(1,9999).'cover'.mt_rand(1,9999).".".$documentFormat;
			$documentSize = $coverFile[0]->getClientSize();
			$coverFile[0]->move($documentUrl,$documentName);
			$documentFormat = strtolower($documentFormat);
			if($documentFormat=='jpg' || $documentFormat=='jpeg' || $documentFormat=='gif' || $documentFormat=='png' || $documentFormat=='pdf' || $documentFormat=='bmp')
			{	
				if(($documentSize/1048576)<=5)
				{
					$documentPersistable->setDocumentName($documentName);
					$documentPersistable->setDocumentSize($documentSize);
					$documentPersistable->setDocumentFormat($documentFormat);
					$documentPersistable->setDocumentUrl($documentUrl);
					$persistableArray[$totalCount] = $documentPersistable;
				}
				else
				{
					return $msgArray['fileSize'];
				}
			}
			else
			{
				return $msgArray['415'];
			}

		}
		return $persistableArray;
	}
	
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Document Persistable object
     */	
	public function createPersistableData(Request $request)
	{
		//trim data
		$tSaleId = trim($request->input()[array_keys($request->input())[0]]);
		$trimArray = array();
		$trimArray[array_keys($request->input())[0]] = $tSaleId;
		return $trimArray;
	}
}