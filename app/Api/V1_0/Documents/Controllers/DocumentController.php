<?php
namespace ERP\Api\V1_0\Documents\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Api\V1_0\Documents\Processors\DocumentProcessor;
use ERP\Core\Documents\Persistables\DocumentPersistable;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Core\Documents\Services\DocumentService;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Documents\DocumentModel;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class DocumentController extends BaseController implements ContainerInterface
{
	/**
	 * get and invoke method is of ContainerInterface method
	 */		
    public function get($id,$name)
	{
		// echo "get";
	}
	public function invoke(callable $method)
	{
		// echo "invoke";
	}
	
	/**
	 * set data for insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function insertUpdate(Request $request,$documentPath)
    {
		// check the requested Http method
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		// insert
		if($requestMethod == 'POST')
		{
			$documentProcessor = new DocumentProcessor();
			$documentPersistable = new DocumentPersistable();	
			$documentService= new DocumentService();			
			$documentPersistable = $documentProcessor->createPersistable($request,$documentPath);
			
			if(is_array($documentPersistable))
			{
				$status = $documentService->insert($documentPersistable);
				return $status;
			}
			else
			{
				return $documentPersistable;
			}
		}
	}
	
	/**
	 * get data with specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function getData(Request $request)
	{
		$RequestUri = explode("/", $_SERVER['REQUEST_URI']);
		if(strcmp($RequestUri[1],"accounting")==0 || strcmp($RequestUri[2],"bills")==0 || strcmp($RequestUri[2],"quotations")==0)
		{
			//get sales data as per given saleId
			$documentProcessor = new DocumentProcessor();
			$documentService= new DocumentService();
			$processedData = $documentProcessor->createPersistableData($request);
			$serviceData = $documentService->getSaleData($processedData[array_keys($request->input())[0]],$request->header());
			return $serviceData;
		}
		else
		{
			//Authentication
			$tokenAuthentication = new TokenAuthentication();
			$authenticationResult = $tokenAuthentication->authenticate($request->header());
			
			//get constant array
			$constantClass = new ConstantClass();
			$constantArray = $constantClass->constantVariable();
			
			if(strcmp($constantArray['success'],$authenticationResult)==0)
			{
				//get sales data as per given saleId
				$documentProcessor = new DocumentProcessor();
				$documentService= new DocumentService();	
				$processedData = $documentProcessor->createPersistableData($request);
				$serviceData = $documentService->getSaleData($processedData['saleId'],$request->header());
				return $serviceData;
			}
			else
			{
				return $authenticationResult;
			}
		}
	}
	
	/**
	 * get data with specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function getbulkPrintData(Request $request)
	{
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		if (array_key_exists('saleIds', $request->input()))
		{
			$saleIds = $request->input()['saleIds'];
			if (count($saleIds) > 0)
			{
				$documentService= new DocumentService();
				$serviceData = $documentService->getDocbulkPrintData($saleIds);
				return $serviceData;
			}
			return $exceptionArray['204'];
		}
		else{
			return $exceptionArray['204'];
		}
		
	}
	/**
	 * get data with specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function getQuotationData(Request $request)
	{
		$RequestUri = explode("/", $_SERVER['REQUEST_URI']);
		if(strcmp($RequestUri[1],"accounting")==0 || strcmp($RequestUri[2],"quotations")==0)
		{
			$headerData = $request->header();
			//get quotations data as per given quotationBillId
			$documentProcessor = new DocumentProcessor();
			$documentService= new DocumentService();	
			$processedData = $documentProcessor->createPersistableData($request);
			$serviceData = $documentService->getQuotationData($processedData[array_keys($request->input())[0]],$request->input()['companyId'],$request->input()['quotationData'],$headerData);
			return $serviceData;
		}
	}
	
	/**
	 * get data with specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function getJobFormDocumentData(Request $request)
	{
		$RequestUri = explode("/", $_SERVER['REQUEST_URI']);
		if(strcmp($RequestUri[1],"crm")==0 || strcmp($RequestUri[2],"job-form")==0)
		{
			// get quotations data as per given quotationBillId
			$documentProcessor = new DocumentProcessor();
			$documentService= new DocumentService();	
			$serviceData = $documentService->getJobformData($request->input());
			return $serviceData;
		}
	}
	
	/**
	 * delete specified resource 
	 * @param  Request object[Request $request] and document-id
	*/
	public function deleteDocument(Request $request,$documentId)
	{
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$documentModel= new DocumentModel();	
			$deletedResult = $documentModel->deleteDocumentData($request->header(),$documentId);
			return $deletedResult;
		}
		else
		{
			return $authenticationResult;
		}
	}
}
