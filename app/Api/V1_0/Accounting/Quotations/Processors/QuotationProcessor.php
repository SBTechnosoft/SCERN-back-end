<?php
namespace ERP\Api\V1_0\Accounting\Quotations\Processors;
	
use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Accounting\Quotations\Persistables\QuotationPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Accounting\Quotations\Validations\QuotationValidate;
use ERP\Api\V1_0\Accounting\Quotations\Transformers\QuotationTransformer;
use ERP\Model\Clients\ClientModel;
use Illuminate\Container\Container;
use ERP\Api\V1_0\Clients\Controllers\ClientController;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use Carbon;
use ERP\Core\Clients\Entities\ClientArray;
use ERP\Api\V1_0\Documents\Controllers\DocumentController;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
	
class QuotationProcessor extends BaseProcessor
{	/**
     * @var quotationPersistable
	 * @var request
	*/
	private $quotationPersistable;
	private $request;    
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Quotation Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;
		$clientContactFlag=0;
		$contactFlag=0;
		$taxFlag=0;

		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();

		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();	
		//trim an input 
		$quotationTransformer = new QuotationTransformer();
		$tRequest = $quotationTransformer->trimInsertData($this->request);
		if($tRequest==1)
		{
			return $msgArray['content'];
		}	
		else
		{
			//validation
			$quotationValidate = new QuotationValidate();
			$status = $quotationValidate->validate($tRequest);
			if($status==$constantArray['success'])
			{
				//get contact-number from input data
				if(!array_key_exists($constantArray['contactNo'],$tRequest))
				{
					$contactNo="";
				}
				else
				{
					$contactNo = $tRequest['contact_no'];
				}
				if($contactNo=="" || $contactNo==0)
				{
					//client insertion
					$clientResult = $this->clientInsertion($tRequest);
					if(strcmp($clientResult,$msgArray['content'])==0)
					{
						return $clientResult;
					}
					$clientId = json_decode($clientResult)->clientId;
						
				}
				else
				{
					//check client is exists by contact-number
					$clientModel = new ClientModel();
					$clientArrayData = $clientModel->getClientData($contactNo);
					$clientData = (json_decode($clientArrayData));
					if(is_array($clientData) || is_object($clientData))
					{
						if(is_object($clientData))
						{
							$clientObjectData = $clientData->clientData;
						}
						else if(is_array($clientData))
						{
							$clientObjectData = $clientData['clientData'];
						}
						//update client-data
						$encodedClientData = $clientObjectData;
						$clientId = $encodedClientData[0]->client_id;
						$clientUpdateResult = $this->clientUpdate($tRequest,$clientId);
						if(strcmp($clientUpdateResult,$msgArray['200'])!=0)
						{
							return $clientUpdateResult;
						}
					}
					else
					{
						//client insertion
						$clientResult = $this->clientInsertion($tRequest);
						if(strcmp($clientResult,$msgArray['content'])==0)
						{
							return $clientResult;
						}
						$clientId = json_decode($clientResult)->clientId;
					}
				}
			}
			else
			{
				//data is not valid...return validation error message
				return $status;
			}
		}
		$productArray = array();
		if(array_key_exists("issalesorder",$request->header()))
		{
			$productArray['invoiceNumber']=$tRequest['invoice_number'];
		}
		else
		{
			$productArray['quotationNumber']=$tRequest['quotation_number'];
		}
		$productArray['transactionType']=$constantArray['journalOutward'];
		$productArray['companyId']=$tRequest['company_id'];	
		$productArray['branchId']=$tRequest['branch_id'];	
		
		$tInventoryArray = array();
		for($trimData=0;$trimData<count($request->input()['inventory']);$trimData++)
		{
			$tInventoryArray[$trimData] = array();

			$tInventoryArray[$trimData][5] = array_key_exists("color", $request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['color']) : "XX";
			$tInventoryArray[$trimData][6] = array_key_exists("frameNo", $request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['frameNo']) : "";
			$tInventoryArray[$trimData][7] = array_key_exists("size", $request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['size']) : "ZZ";
			$tInventoryArray[$trimData][8] = array_key_exists("cgstPercentage",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['cgstPercentage']):0;
			$tInventoryArray[$trimData][9] = array_key_exists("cgstAmount",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['cgstAmount']):0;
			$tInventoryArray[$trimData][10] = array_key_exists("sgstPercentage",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['sgstPercentage']):0;
			$tInventoryArray[$trimData][11] = array_key_exists("sgstAmount",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['sgstAmount']):0;
			$tInventoryArray[$trimData][12] = array_key_exists("igstPercentage",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['igstPercentage']):0;
			$tInventoryArray[$trimData][13] = array_key_exists("igstAmount",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['igstAmount']):0;
			array_push($request->input()['inventory'][$trimData],$tInventoryArray[$trimData]);
		}
		$productArray['inventory'] = $request->input()['inventory'];
		$docFlag=0;
		$documentPath = $constantArray['billDocumentUrl'];
		if(in_array(true,$request->file()) || array_key_exists('scanFile',$request->input()))
		{
			$documentController =new DocumentController(new Container());
			$processedData = $documentController->insertUpdate($request,$documentPath);
			if(is_array($processedData))
			{
				$docFlag=1;
			}
			else
			{
				return $processedData;
			}
		}
		//entry date conversion
		$transformEntryDate = Carbon\Carbon::createFromFormat('d-m-Y', $tRequest['entry_date'])->format('Y-m-d');
		$quotationPersistable = new QuotationPersistable();
		$quotationPersistable->setProductArray(json_encode($productArray));
		$quotationPersistable->setQuotationNumber($tRequest['quotation_number']);		
		$quotationPersistable->setTotal($tRequest['total']);
		$quotationPersistable->setTotalDiscounttype($tRequest['total_discounttype']);
		$quotationPersistable->setTotalDiscount($tRequest['total_discount']);
		$quotationPersistable->setTotalCgstPercentage($tRequest['totalCgstPercentage']);
		$quotationPersistable->setTotalSgstPercentage($tRequest['totalSgstPercentage']);
		$quotationPersistable->setTotalIgstPercentage($tRequest['totalIgstPercentage']);
		$quotationPersistable->setExtraCharge($tRequest['extra_charge']);
		$quotationPersistable->setTax($tRequest['tax']);		
		$quotationPersistable->setGrandTotal($tRequest['grand_total']);
		$quotationPersistable->setRemark($tRequest['remark']);
		$quotationPersistable->setEntryDate($transformEntryDate);
		$quotationPersistable->setClientId($clientId);
		$quotationPersistable->setCompanyId($tRequest['company_id']);	
		$quotationPersistable->setBranchId($tRequest['branch_id']);	
		$quotationPersistable->setInvoiceNumber($tRequest['invoice_number']);		
		$quotationPersistable->setPoNumber($tRequest['po_number']);		
		$quotationPersistable->setPaymentMode($tRequest['payment_mode']);		
		$quotationPersistable->setBankName($tRequest['bank_name']);		
		$quotationPersistable->setCheckNumber($tRequest['check_number']);		
				
		$quotationPersistable->setJfId(0);	
		
		if($docFlag==1)
		{
			array_push($processedData,$quotationPersistable);
			return $processedData;	
		}
		else
		{
			return $quotationPersistable;
		}		
		return $quotationPersistable;
	}
	
	/**
     * get request data & quotation-bill-id and set into the persistable object
     * $param Request object [Request $request] and quotation-bill-id and quotationdata
     * @return Quotation Persistable object/error message
     */
	public function createPersistableChange(Request $request,$quotationBillId,$quotationData)
	{
		$balanceFlag=0;
		
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();

		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		//trim quotation data
		$quotationTransformer = new QuotationTransformer();
		$quotationTrimData = $quotationTransformer->trimQuotationUpdateData($request);

		// $ledgerModel = new LedgerModel();
		$clientArray = new ClientArray();
		$clientArrayData = $clientArray->getClientArrayData();
		$clientData = array();
		foreach($clientArrayData as $key => $value)
		{
			if(array_key_exists($key,$quotationTrimData))
			{
				$clientData[$value] = $quotationTrimData[$key];
			}
		}	
		$contactFlag=0;
		$clientModel = new ClientModel();
		//get quotation-data as per given quotationBillId
		$quotationData = json_decode($quotationData);	
		//get client-data as per given client-id for getting client contact_no
		$clientIdData = $clientModel->getData($quotationData[0]->client_id);
		$decodedClientData = (json_decode($clientIdData));
		$contactNo = $decodedClientData->clientData[0]->contact_no;

		if(count($clientData)!=0)
		{
			//check contact_no exist or not
			if(array_key_exists("contact_no",$clientData))
			{
				$contactNo = $clientData['contact_no'];
			}
			//get client-data as per contact-no
			$clientDataAsPerContactNo = $clientModel->getClientData($contactNo);
			if(strcmp($clientDataAsPerContactNo,$msgArray['200'])!=0)
			{
				$clientDecodedData = json_decode($clientDataAsPerContactNo);
				//update client-data
				$encodedClientData = $clientDecodedData->clientData;
				$clientId = $encodedClientData[0]->client_id;
				$clientUpdateResult = $this->clientUpdateData($clientData,$clientId);
				if(strcmp($clientUpdateResult,$msgArray['200'])!=0)
				{
					return $clientUpdateResult;
				}
			}
			else
			{
				//client insertion
				$clientResult = $this->clientInsertion($clientData);
				if(strcmp($clientResult,$msgArray['content'])==0)
				{
					return $clientResult;
				}
				$clientId = json_decode($clientResult)->clientId;
			}
		}
		//validate bill data
		//........pending
		$quoFlag=0;
		//set bill data into persistable object
		$quotationPersistable = array();
		$clientBillArrayData = $clientArray->getBillClientArrayData();
		
		//splice data from trim array
		for($index=0;$index<count($clientBillArrayData);$index++)
		{
			for($innerIndex=0;$innerIndex<count($quotationTrimData);$innerIndex++)
			{
				if(strcmp('inventory',array_keys($quotationTrimData)[$innerIndex])!=0)
				{
					if(strcmp(array_keys($quotationTrimData)[$innerIndex],array_keys($clientBillArrayData)[$index])==0)
					{
						array_splice($quotationTrimData,$innerIndex,1);
						break;
					}
				}
			}
		}

		for($quotationArrayData=0;$quotationArrayData<count($quotationTrimData);$quotationArrayData++)
		{
			// making an object of persistable
			$quotationPersistable[$quotationArrayData] = new QuotationPersistable();
			if(strcmp('inventory',array_keys($quotationTrimData)[$quotationArrayData])!=0)
			{
				$str = str_replace(' ', '', ucwords(str_replace('_', ' ', array_keys($quotationTrimData)[$quotationArrayData])));	
				$setFuncName = "set".$str;
				$getFuncName = "get".$str;
				$quotationPersistable[$quotationArrayData]->$setFuncName($quotationTrimData[array_keys($quotationTrimData)[$quotationArrayData]]);
				$quotationPersistable[$quotationArrayData]->setName($getFuncName);
				$quotationPersistable[$quotationArrayData]->setKey(array_keys($quotationTrimData)[$quotationArrayData]);
				$quotationPersistable[$quotationArrayData]->setQuotationId($quotationBillId);
			}
			else
			{
				for($inventoryData=0;$inventoryData<count($request->input()['inventory']);$inventoryData++)
				{
					$quotationTrimData['inventory'][$inventoryData]['amount'] = $request->input()['inventory'][$inventoryData]['amount'];
					$quotationTrimData['inventory'][$inventoryData]['productName'] = $request->input()['inventory'][$inventoryData]['productName'];
					$quotationTrimData['inventory'][$inventoryData]['measurementUnit'] = $request->input()['inventory'][$inventoryData]['measurementUnit'];
					$quotationTrimData['inventory'][$inventoryData]['cgstPercentage'] = array_key_exists("cgstPercentage",$request->input()['inventory'][$inventoryData])?trim($request->input()['inventory'][$inventoryData]['cgstPercentage']):0;
					$quotationTrimData['inventory'][$inventoryData]['cgstAmount'] = array_key_exists("cgstAmount",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['cgstAmount']):0;
					$quotationTrimData['inventory'][$inventoryData]['sgstPercentage'] = array_key_exists("sgstPercentage",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['sgstPercentage']):0;
					$quotationTrimData['inventory'][$inventoryData]['sgstAmount'] = array_key_exists("sgstAmount",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['sgstAmount']):0;
					$quotationTrimData['inventory'][$inventoryData]['igstPercentage'] = array_key_exists("igstPercentage",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['igstPercentage']):0;
					$quotationTrimData['inventory'][$inventoryData]['igstAmount'] = array_key_exists("igstAmount",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['igstAmount']):0;
				}
				$quoFlag=1;
				$decodedProductArrayData = json_decode($quotationData[0]->product_array);
				$productArray = array();
				if(array_key_exists("issalesorder",$request->header()))
				{
					$productArray['invoiceNumber']=$decodedProductArrayData->invoiceNumber;
				}
				else
				{
					$productArray['quotationNumber'] = $decodedProductArrayData->quotationNumber;
				}
				$productArray['transactionType'] = $decodedProductArrayData->transactionType;
				$productArray['companyId'] = $decodedProductArrayData->companyId;
				$productArray['inventory'] = $quotationTrimData['inventory'];
				$quotationPersistable[$quotationArrayData]->setProductArray(json_encode($productArray));
				$quotationPersistable[$quotationArrayData]->setQuotationId($quotationBillId);
			}
		}

		$docFlag=0;
		$documentPath = $constantArray['billDocumentUrl'];
		if(in_array(true,$request->file()))
		{
			$documentController = new DocumentController(new Container());
			$processedData = $documentController->insertUpdate($request,$documentPath);
			if(is_array($processedData))
			{
				$docFlag=1;
			}
			else
			{
				return $processedData;
			}
		}
		if($docFlag==1)
		{
			if($quoFlag==1)
			{
				$quotationPersistable[count($quotationPersistable)] = 'flag';
			}
			array_push($processedData,$quotationPersistable);
			return $processedData;
		}
		if($quoFlag==1)
		{
			$quotationPersistable[count($quotationPersistable)] = 'flag';
			return $quotationPersistable;
		}
		else
		{
			return $quotationPersistable;
		}
		
	}
	
	/**
     * client insertion
     * $param trim request array
     * @return result array/error-message
     */	
	public function clientInsertion($tRequest)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$clientArray = array();
		$clientArray['clientName']=$tRequest['client_name'];
		$clientArray['companyName']=array_key_exists('company_name',$tRequest)?$tRequest['company_name']:'';
		$clientArray['emailId']=array_key_exists('email_id',$tRequest)?$tRequest['email_id']:'';
		$clientArray['gst']=array_key_exists('gst',$tRequest)?$tRequest['gst']:'';
		$clientArray['contactNo']=$tRequest['contact_no'];
		$clientArray['contactNo1']=array_key_exists('contactNo1',$tRequest)?$tRequest['contact_no1']:'';
		$clientArray['address1']=array_key_exists('address1',$tRequest)?$tRequest['address1']:'';
		$clientArray['birthDate']=array_key_exists('birth_date',$tRequest)?$tRequest['birth_date']:'0000-00-00';
		$clientArray['anniversaryDate']=array_key_exists('anniversary_date',$tRequest)?$tRequest['anniversary_date']:'0000-00-00';
		$clientArray['otherDate']=array_key_exists('other_date',$tRequest)?$tRequest['other_date']:'0000-00-00';
		$clientArray['isDisplay']=array_key_exists('is_display',$tRequest)?$tRequest['is_display']:$constantArray['isDisplayYes'];
		$clientArray['stateAbb']=$tRequest['state_abb'];
		$clientArray['cityId']=$tRequest['city_id'];
		if(array_key_exists('profession_id',$tRequest))
		{
			$clientArray['professionId']=$tRequest['profession_id'];
		}
		$clientController = new ClientController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['clientUrl'];
		$clientRequest = Request::create($path,$method,$clientArray);
		$processedData = $clientController->store($clientRequest);
		return $processedData;
	}
	
	/**
     * client update
     * $param trim request array,client_id
     * @return result array/error-message
     */	
	public function clientUpdate($tRequest,$clientId)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// update client data
		$clientArray = array();
		if(array_key_exists('client_name',$tRequest))
		{
			$clientArray['clientName']=$tRequest['client_name'];
		}
		if(array_key_exists('company_name',$tRequest))
		{
			$clientArray['companyName']=$tRequest['company_name'];
		}
		if(array_key_exists('email_id',$tRequest))
		{
			$clientArray['emailId']=$tRequest['email_id'];
		}
		if(array_key_exists('gst',$tRequest))
		{
			$clientArray['gst']=$tRequest['gst'];
		}
		if(array_key_exists('contact_no',$tRequest))
		{
			$clientArray['contactNo']=$tRequest['contact_no'];
		}
		if(array_key_exists('contact_no1',$tRequest))
		{
			$clientArray['contactNo1']=$tRequest['contact_no1'];
		}
		if(array_key_exists('address1',$tRequest))
		{
			$clientArray['address1']=$tRequest['address1'];
		}
		if(array_key_exists('is_display',$tRequest))
		{
			$clientArray['isDisplay']=$tRequest['is_display'];
		}
		if(array_key_exists('state_abb',$tRequest))
		{
			$clientArray['stateAbb']=$tRequest['state_abb'];
		}
		if(array_key_exists('profession_id',$tRequest))
		{
			$clientArray['professionId']=$tRequest['profession_id'];
		}
		if(array_key_exists('city_id',$tRequest))
		{
			$clientArray['cityId']=$tRequest['city_id'];
		}
		if(array_key_exists('birth_date',$tRequest))
		{
			$clientArray['birthDate']=$tRequest['birth_date'];
		}
		if(array_key_exists('anniversary_date',$tRequest))
		{
			$clientArray['anniversaryDate']=$tRequest['anniversary_date'];
		}
		if(array_key_exists('other_date',$tRequest))
		{
			$clientArray['otherDate']=$tRequest['other_date'];
		}
		$clientController = new ClientController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['clientUrl'].'/'.$clientId;
		$clientRequest = Request::create($path,$method,$clientArray);
		$processedData = $clientController->updateData($clientRequest,$clientId);
		return $processedData;
	}

	/**
     * client update
     * $param trim request array,client_id
     * @return result array/error-message
     */	
	public function clientUpdateData($tRequest,$clientId)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// update client data
		$clientArray = array();
		if(array_key_exists('clientName',$tRequest))
		{
			$clientArray['clientName']=$tRequest['clientName'];
		}
		if(array_key_exists('companyName',$tRequest))
		{
			$clientArray['companyName']=$tRequest['companyName'];
		}
		if(array_key_exists('emailId',$tRequest))
		{
			$clientArray['emailId']=$tRequest['emailId'];
		}
		if(array_key_exists('gst',$tRequest))
		{
			$clientArray['gst']=$tRequest['gst'];
		}
		if(array_key_exists('contactNo',$tRequest))
		{
			$clientArray['contactNo']=$tRequest['contactNo'];
		}
		if(array_key_exists('contactNo1',$tRequest))
		{
			$clientArray['contactNo1']=$tRequest['contactNo1'];
		}
		if(array_key_exists('address1',$tRequest))
		{
			$clientArray['address1']=$tRequest['address1'];
		}
		if(array_key_exists('isDisplay',$tRequest))
		{
			$clientArray['isDisplay']=$tRequest['isDisplay'];
		}
		if(array_key_exists('stateAbb',$tRequest))
		{
			$clientArray['stateAbb']=$tRequest['stateAbb'];
		}
		if(array_key_exists('professionId',$tRequest))
		{
			$clientArray['professionId']=$tRequest['professionId'];
		}
		if(array_key_exists('cityId',$tRequest))
		{
			$clientArray['cityId']=$tRequest['cityId'];
		}
		if(array_key_exists('birthDate',$tRequest))
		{
			$clientArray['birthDate']=$tRequest['birthDate'];
		}
		if(array_key_exists('anniversaryDate',$tRequest))
		{
			$clientArray['anniversaryDate']=$tRequest['anniversaryDate'];
		}
		if(array_key_exists('otherDate',$tRequest))
		{
			$clientArray['otherDate']=$tRequest['otherDate'];
		}
		$clientController = new ClientController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['clientUrl'].'/'.$clientId;
		$clientRequest = Request::create($path,$method,$clientArray);
		$processedData = $clientController->updateData($clientRequest,$clientId);
		return $processedData;
	}
}