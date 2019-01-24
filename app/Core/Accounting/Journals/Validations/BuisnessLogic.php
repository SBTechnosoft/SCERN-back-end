<?php
namespace ERP\Core\Accounting\Journals\Validations;

use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Accounting\Ledgers\Services\LedgerService;
use ERP\Entities\Constants\ConstantClass;
use ERP\Api\V1_0\Products\Controllers\ProductController;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Model\Accounting\Journals\JournalModel;
use ERP\Core\Accounting\Ledgers\Entities\LedgerArray;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class BuisnessLogic extends LedgerModel
{
	/**
	 * validate trim-request ledger-data for insert
	 * validate ledger-data(ledger name should be unique as per company_id)
     * @param trim-array
     * @return array/exception message
     */
	public function validateLedgerData($companyId,$ledgerName,$contactNo)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get ledger-data from database
		$ledgerModel = new BuisnessLogic();
		$ledgerResult = $ledgerModel->getLedgerDataId($companyId,$ledgerName,$contactNo);
		
		if(strcmp($ledgerResult,$exceptionArray['404'])==0)
		{
			$trimRequest = array();
			return $trimRequest;
		}
		else
		{
			return $exceptionArray['content'];
		}
	}
	
	/**
	 * validate trim-request ledger-data for update
	 * validate ledger-data(ledger name should be unique as per company_id)
     * @param trim-array and ledgerId
     * @return array/exception message
     */
	public function validateUpdateLedgerData($ledgerName,$ledgerId,$input)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get ledger-data from database
		$ledgerModel = new BuisnessLogic();
		$ledgerCompanyData = $ledgerModel->getData($ledgerId);
		if(is_object(json_decode($ledgerCompanyData)))
		{
			if(array_key_exists('contactNo',$input))
			{
				$contactNo = $input['contactNo'];
			}
			else
			{
				//get contact-no of existing ledger as per given ledger-id
				$ledgerData = json_decode($ledgerCompanyData);
				$contactNo = $ledgerData->contact_no;
			}
			$ledgerResult = $ledgerModel->getLedgerDataId(json_decode($ledgerCompanyData)->company_id,$ledgerName,$contactNo);
			$trimRequest = array();
			if(strcmp($ledgerResult,$exceptionArray['404'])==0)
			{
				return $trimRequest;
			}
			else
			{
				if(strcmp(json_decode($ledgerResult)[0]->ledger_id,$ledgerId)==0)
				{
					return $trimRequest;
				}
				else
				{
					return $exceptionArray['content'];
				}
			}
		}
		else
		{
			return $ledgerCompanyData;
		}
		return $trimRequest;
	}
	
	/**
	 * validate trim-request data for insert
     * @param trim-array
     * @return array/exception message
     */
	public function validateBuisnessLogic($trimRequest)
	{
		$ledgerId = array();
		$creditAmountArray = 0;
		$debitAmountArray = 0;
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		for($journalArray=0;$journalArray<count($trimRequest[0]);$journalArray++)
		{
			$amount[$journalArray][0] = $trimRequest[0][$journalArray]['amount'];
			$amountType[$journalArray][1] = $trimRequest[0][$journalArray]['amountType'];
			$ledgerId[$journalArray][2] = $trimRequest[0][$journalArray]['ledgerId'];
			
			//check ledger exists
			$journalObject = new BuisnessLogic();
			$ledgerIdResult = $journalObject->getData($ledgerId[$journalArray][2]);
			
			if(strcmp($ledgerIdResult,$exceptionArray['404'])==0)
			{
				return $exceptionArray['404'];
			}
			else
			{
				//check credit-debit amount
				if(strcmp($amountType[$journalArray][1],"credit")==0)
				{
					$creditAmountArray = $creditAmountArray+$amount[$journalArray][0];
				}
				else
				{
					$debitAmountArray = $debitAmountArray+$amount[$journalArray][0];
				}
			}
		}
		$epsilon = 0.00001;
		if(abs($creditAmountArray-$debitAmountArray)<$epsilon)
		{
			return $trimRequest;
		}
		else
		{
			return $exceptionArray['equal'];
		}
	}
	
	/**
	 * validate trim-request data for update
     * @param trim-array
     * @return array/exception message
     */
	public function validateUpdateBuisnessLogic($trimRequest)
	{
		$ledgerId = array();
		$creditAmountArray = 0;
		$debitAmountArray = 0;
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//array exist
		if(array_key_exists('0',$trimRequest))
		{
			if(array_key_exists('flag',$trimRequest))
			{
				for($journalArray=0;$journalArray<count($trimRequest[0]);$journalArray++)
				{
					$amount[$journalArray][0] = $trimRequest[0][$journalArray]['amount'];
					$amountType[$journalArray][1] = $trimRequest[0][$journalArray]['amount_type'];
					$ledgerId[$journalArray][2] = $trimRequest[0][$journalArray]['ledger_id'];
					//check ledger exists
					$journalObject = new BuisnessLogic();
					$ledgerIdResult = $journalObject->getData($ledgerId[$journalArray][2]);
					if(strcmp($ledgerIdResult,$exceptionArray['404'])==0)
					{
						return $exceptionArray['404'];
					}
					else
					{
						//check credit-debit amount
						if(strcmp($amountType[$journalArray][1],"credit")==0)
						{
							$creditAmountArray = $creditAmountArray+$amount[$journalArray][0];
						}
						else
						{
							$debitAmountArray = $debitAmountArray+$amount[$journalArray][0];
						}
					}
				}
				$epsilon = 0.00001;
				if(abs($creditAmountArray-$creditAmountArray)<$epsilon)
				{
					return $trimRequest;
				}	
				else
				{
					return $exceptionArray['equal'];
				}
			}
			else
			{
				for($journalArray=0;$journalArray<count($trimRequest);$journalArray++)
				{
					$amount[$journalArray][0] = $trimRequest[$journalArray]['amount'];
					$amountType[$journalArray][1] = $trimRequest[$journalArray]['amount_type'];
					$ledgerId[$journalArray][2] = $trimRequest[$journalArray]['ledger_id'];
					
					//check ledger exists
					$journalObject = new BuisnessLogic();
					$ledgerIdResult = $journalObject->getData($ledgerId[$journalArray][2]);
					if(strcmp($ledgerIdResult,$exceptionArray['404'])==0)
					{
						return $exceptionArray['404'];
					}
					else
					{
						//check credit-debit amount
						if(strcmp($amountType[$journalArray][1],"credit")==0)
						{
							$creditAmountArray = $creditAmountArray+$amount[$journalArray][0];
						}
						else
						{
							$debitAmountArray = $debitAmountArray+$amount[$journalArray][0];
						}
					}
				}
				$epsilon = 0.00001;
				if(abs($creditAmountArray-$debitAmountArray)<$epsilon)
				{
					return $trimRequest;
				}
				else
				{
					return $exceptionArray['equal'];
				}
			}
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * validate trim-request data for update
     * @param trim-array of product and journal and header data
	 * check journal and product data and if tax and discount is available then check that value
     * @return array/exception message
     */
	public function validateUpdateJournalBuisnessLogic($headerData,$trimJournalData,$productData,$jfId)
	{
		$ledgerIdArray = array();
		$discountArray = array();
		$taxFlag=0;
		$discountTotal=0;
		$discountFlag=0;
		$journalDiscountFlag=0;
		$journalTaxFlag=0;
		$disFlag=0;
		$disFlag1=0;
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		// get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$ledgerService = new LedgerService();
		//tax and array both exist
		if(array_key_exists("tax",$productData) && array_key_exists("0",$productData))
		{
			if(array_key_exists("flag",$productData))
			{
				$arrayProductData=$productData[0];
			}
			else
			{
				$arrayProductData=$productData;
			}
			
			if(array_key_exists("flag",$trimJournalData))
			{
				$trimData=$trimJournalData[0];
			}
			else
			{
				$trimData=$trimJournalData;
			}
			//calculate total discount amount
			for($arrayData=0;$arrayData<count($arrayProductData);$arrayData++)
			{
				// print_r($arrayProductData[$arrayData]['discount_value']);
				$discountTotal = $discountTotal+$arrayProductData[$arrayData]['discount_value'];
			}
			//check tax and discount is available in journal data
			for($journalArrayData=0;$journalArrayData<count($trimData);$journalArrayData++)
			{
				$ledgerIdArray[$journalArrayData] = $trimData[$journalArrayData]['ledger_id'];
				$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
				if(strcmp("sales",$headerData['type'][0])==0)
				{
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==17)
					{
						//tax  ledger exist
						if($trimData[$journalArrayData]['amount']==$productData['tax'])
						{
							$taxFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==16)
					{
						//discount ledger exist
						if(trim($trimData[$journalArrayData]['amount'])==trim($discountTotal))
						{
							$discountFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)!=16 && trim($discountTotal)==0)
					{
						if($disFlag==(count($trimData)-1))
						{
							$discountFlag=1;
						}
						$disFlag++;
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)!=17 && trim($productData['tax'])==0)
					{
						if($disFlag1==(count($trimData)-1))
						{
							$taxFlag=1;
						}
						$disFlag1++;
					}
				}
				else
				{
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==16)
					{
						//tax  ledger exist
						if($trimData[$journalArrayData]['amount']==$productData['tax'])
						{
							$taxFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==17)
					{
						//discount ledger exist
						if(trim($trimData[$journalArrayData]['amount'])==trim($discountTotal))
						{
							$discountFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)!=17 && trim($discountTotal)==0)
					{
						if($disFlag==(count($trimData)-1))
						{
							$discountFlag=1;
						}
						$disFlag++;
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)!=16 && trim($productData['tax'])==0)
					{
						if($disFlag1==(count($trimData)-1))
						{
							$taxFlag=1;
						}
						$disFlag1++;
					}
				}
				
			}
			if(count($trimData)==$disFlag && $taxFlag==1 || count($trimData)==$disFlag && count($trimData)==$disFlag1)
			{
			}
			else if($discountFlag==0 || $taxFlag==0)
			{
				return $exceptionArray['content'];
			}
			else
			{
			}
		}
		//only tax exist
		else if(array_key_exists("tax",$productData))
		{
			// get productArray and validate it with journal array
			$productController = new ProductController(new Container());
			$method=$constantArray['getMethod'];
			$path=$constantArray['productUrl'];
			$productId = array();
			$productRequest = Request::create($path,$method,$productId);
			$productRequest->headers->set('jfid',$jfId);
			$processedData = $productController->getData($productRequest);
			$jsonDecodedProductData = json_decode($processedData);
			
			if(array_key_exists("flag",$trimJournalData))
			{
				$trimData=$trimJournalData[0];
			}
			else
			{
				$trimData=$trimJournalData;
			}
			
			//check tax 
			for($journalArrayData=0;$journalArrayData<count($trimData);$journalArrayData++)
			{
				$ledgerIdArray[$journalArrayData] = $trimData[$journalArrayData]['ledger_id'];
				$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
				if(strcmp("sales",$headerData['type'][0])==0)
				{
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==17)
					{
						//tax ledger exist
						if($trimData[$journalArrayData]['amount']==$productData['tax'])
						{
							$taxFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==16)
					{
						$journalDiscountFlag=1;
						if(is_array($jsonDecodedProductData))
						{
							for($productArrayData=0;$productArrayData<count($jsonDecodedProductData);$productArrayData++)
							{
								$discountTotal = $discountTotal+$jsonDecodedProductData[$productArrayData]->discountValue;
							}
							if(trim($trimData[$journalArrayData]['amount'])==trim($discountTotal))
							{
								$discountFlag=1;
							}
						}
						else
						{
							return $processedData;
						}
					}
				}
				else
				{
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
					{
						//tax ledger exist
						if(trim($trimData[$journalArrayData]['amount'])==trim($productData['tax']))
						{
							$taxFlag=1;
						}
					}
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
					{
						$journalDiscountFlag=1;
						if(is_array($jsonDecodedProductData))
						{
							for($productArrayData=0;$productArrayData<count($jsonDecodedProductData);$productArrayData++)
							{
								$discountTotal = $discountTotal+$jsonDecodedProductData[$productArrayData]->discountValue;
							}
							if(trim($trimData[$journalArrayData]['amount'])==trim($discountTotal))
							{
								$discountFlag=1;
							}
						}
						else
						{
							return $processedData;
						}
					}
				}
			}
			if($taxFlag==0 || $journalDiscountFlag==1 && $discountFlag==0)
			{
				return $exceptionArray['content'];
			}
		}
		//only array exist
		else
		{
			// get productArray and validate it with journal array
			$productController = new ProductController(new Container());
			$method=$constantArray['getMethod'];
			$path=$constantArray['productUrl'];
			$productId = array();
			$productRequest = Request::create($path,$method,$productId);
			$productRequest->headers->set('jfid',$jfId);
			$processedData = $productController->getData($productRequest);
			$jsonDecodedProductData = json_decode($processedData);
			// print_r($productData);
			if(array_key_exists("flag",$trimJournalData))
			{
				$trimData = $trimJournalData[0];
			}
			else
			{
				$trimData = $trimJournalData;
			}
			
			if(array_key_exists("flag",$productData))
			{
				$productArrayData = $productData[0];
			}
			else
			{
				$productArrayData = $productData;
			}
			//calculate total discount amount
			for($arrayData=0;$arrayData<count($productArrayData);$arrayData++)
			{
				
				$discountTotal = $discountTotal+$productArrayData[$arrayData]['discount_value'];
			}
			
			//check tax and discount is available in journal data
			for($journalArrayData=0;$journalArrayData<count($trimData);$journalArrayData++)
			{
				$ledgerIdArray[$journalArrayData] = $trimData[$journalArrayData]['ledger_id'];
				$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
				if(strcmp("sales",$headerData['type'][0])==0)
				{
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==16)
					{
						//discount ledger exist
						if($trimData[$journalArrayData]['amount']==$discountTotal)
						{
							$discountFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)!=16 && trim($discountTotal)==0)
					{
						$disFlag++;
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==17)
					{
						// discount ledger exist
						$journalTaxFlag=1;
						if(is_array($jsonDecodedProductData))
						{
							if(trim($trimData[$journalArrayData]['amount'])==trim($jsonDecodedProductData[0]->tax))
							{
								$taxFlag=1;
							}
						}
						else
						{
							return $processedData;
						}
					}
				}
				else
				{
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==17)
					{
						// discount ledger exist
						if(trim($trimData[$journalArrayData]['amount'])==trim($discountTotal))
						{
							$discountFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)!=17 && trim($discountTotal)==0)
					{
						$disFlag++;
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==16)
					{
						// discount ledger exist
						$journalTaxFlag=1;
						if(is_array($jsonDecodedProductData))
						{
							if(trim($trimData[$journalArrayData]['amount'])==trim($jsonDecodedProductData[0]->tax))
							{
								$taxFlag=1;
							}
						}
						else
						{
							return $processedData;
						}
					}
				}
			}
			if(count($trimData)==$disFlag)
			{
			}
			else if($discountFlag==0)
			{
				return $exceptionArray['content'];
			}
			if($journalTaxFlag==1 && $taxFlag==0)
			{
				return $exceptionArray['content'];
			}
		}
		return $trimJournalData;
	}
	
	/**
	 * validate product data for update
     * @param trim-array of product and journal and header data
	 * check journal and product data and if tax and discount is available then check that value
     * @return array/exception message
     */
	public function validateUpdateProductBuisnessLogic($headerData,$trimJournalData,$productData,$jfId)
	{
		$journalDiscountFlag=0;
		$taxFlag=0;
		$disFlag=0;
		$discountFlag=0;
		$ledgerIdArray = array();
		$discountArray = array();
		$ledgerService = new LedgerService();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		// get journalArray for validate it with product array
		$journalModel = new JournalModel();
		$journalArrayData = $journalModel->getJfIdArrayData($jfId);
		
		// get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// get productArray and validate it with journal array
		//$productController = new ProductController(new Container());
		//$method=$constantArray['getMethod'];
		//$path=$constantArray['productUrl'];
		//$productId = array();
		//$productRequest = Request::create($path,$method,$productId);
		//$productRequest->headers->set('jfid',$jfId);
		//$processedData = $productController->getData($productRequest);
		//$jsonDecodedProductData = json_decode($processedData);
		$decodedJournalData = json_decode($journalArrayData);
		
		
		if(strcmp($journalArrayData,$exceptionArray['404'])==0)
		{
			return $journalArrayData;
		}
		//tax and array both exist
		if(array_key_exists("tax",$productData) && array_key_exists("0",$productData))
		{
			if(array_key_exists("flag",$productData))
			{
				$arrayProductData = $productData[0];
			}
			else
			{
				$arrayProductData = $productData;
			}
			$discountTotal=0;
			//discount ledger exist
			for($productArrayData=0;$productArrayData<count($arrayProductData);$productArrayData++)
			{
				$discountTotal = $discountTotal+$arrayProductData[$productArrayData]['discount_value'];
			}
			for($journalArrayData=0;$journalArrayData<count($decodedJournalData);$journalArrayData++)
			{
				$ledgerIdArray[$journalArrayData] = $decodedJournalData[$journalArrayData]->ledger_id;
				$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
				if(strcmp("sales",$headerData)==0)
				{
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
					{
						//tax ledger exist
						if(trim($decodedJournalData[$journalArrayData]->amount)==trim($productData['tax']))
						{
							$taxFlag=1;
						}
					}
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
					{
						if(trim($discountTotal) == trim($decodedJournalData[$journalArrayData]->amount))
						{
							$discountFlag=1;
						}
					}
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId!=16 && $discountTotal==0)
					{
						$disFlag++;
					}
				}
				else
				{
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
					{
						//tax ledger exist
						if(trim($decodedJournalData[$journalArrayData]->amount)==trim($productData['tax']))
						{
							$taxFlag=1;
						}
					}
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
					{
						if(trim($discountTotal) == trim($decodedJournalData[$journalArrayData]->amount))
						{
							$discountFlag=1;
						}
					}
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId!=17 && $discountTotal==0)
					{
						$disFlag++;
					}
				}
			}
			if(count($decodedJournalData)==$disFlag && $taxFlag==1)
			{
			}
			else if($discountFlag==0 || $taxFlag==0)
			{
				return $exceptionArray['content'];
			}
			else
			{
			}
		}
		//only tax exist
		else if(array_key_exists("tax",$productData))
		{
			for($journalArrayData=0;$journalArrayData<count($decodedJournalData);$journalArrayData++)
			{
				$ledgerIdArray[$journalArrayData] = $decodedJournalData[$journalArrayData]->ledger_id;
				$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
				if(strcmp("sales",$headerData)==0)
				{
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
					{
						// tax ledger exist
						if(trim($decodedJournalData[$journalArrayData]->amount)==trim($productData['tax']))
						{
							$taxFlag=1;
						}
					}
				}
				else
				{
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
					{
						// tax ledger exist
						if(trim($decodedJournalData[$journalArrayData]->amount)==trim($productData['tax']))
						{
							$taxFlag=1;
						}
					}
				}
			} 
			if($taxFlag==0)
			{
				return $exceptionArray['content'];
			}
		}
		//only array exist
		else
		{
			if(array_key_exists("flag",$productData))
			{
				$arrayProductData = $productData[0];
			}
			else
			{
				$arrayProductData = $productData;
			}
			$discountTotal=0;
			//discount ledger exist
			for($productArrayData=0;$productArrayData<count($arrayProductData);$productArrayData++)
			{
				$discountTotal = $discountTotal+$arrayProductData[$productArrayData]['discount_value'];
			}
			for($journalArrayData=0;$journalArrayData<count($decodedJournalData);$journalArrayData++)
			{
				$ledgerIdArray[$journalArrayData] = $decodedJournalData[$journalArrayData]->ledger_id;
				$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
				if(strcmp("sales",$headerData)==0)
				{
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
					{
						if(trim($discountTotal) == trim($decodedJournalData[$journalArrayData]->amount))
						{
							$discountFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)!=16 && trim($discountTotal)==0)
					{
						$disFlag++;
					}
				}
				else
				{
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)==17)
					{
						if(trim($discountTotal) == trim($decodedJournalData[$journalArrayData]->amount))
						{
							$discountFlag=1;
						}
					}
					if(trim(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId)!=17 && trim($discountTotal)==0)
					{
						$disFlag++;
					}
				}
			}
			if(count($decodedJournalData)==$disFlag)
			{
			}
			else if($discountFlag==0)
			{
				return $exceptionArray['content'];
			}
			else
			{
			}
		}
		return $trimJournalData;
	}
	
	/**
	 * validate jouranl data for update
     * @param trim-array of journal and header data
	 * check journal and product data and if tax and discount is available then check that value
     * @return array/exception message
     */
	public function validateJournalBuisnessLogic($headerData,$journalData,$jfId)
	{
		$discountFlag=0;
		$journalTaxFlag=0;
		$journalDiscountFlag=0;
		$taxFlag=0;
		$discountArray = array();
		$ledgerService = new LedgerService();
		$ledgerIdArray = array();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		// get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// get productArray and validate it with journal array
		$productController = new ProductController(new Container());
		$method=$constantArray['getMethod'];
		$path=$constantArray['productUrl'];
		$productId = array();
	
		$productRequest = Request::create($path,$method,$productId);
		$productRequest->headers->set('jfid',$jfId);
		$processedData = $productController->getData($productRequest);
		$jsonDecodedProductData = json_decode($processedData);
		
		if(array_key_exists("flag",$journalData))
		{
			$journalDataArray = $journalData[0];
		}
		else
		{
			$journalDataArray = $journalData;
		}
		for($journalArrayData=0;$journalArrayData<count($journalDataArray);$journalArrayData++)
		{
			$ledgerIdArray[$journalArrayData] = $journalDataArray[$journalArrayData]['ledger_id'];
			$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
			if(strcmp("sales",$headerData['type'][0])==0)
			{
				if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
				{
					$journalTaxFlag=1;
					//tax ledger exist
					if(trim($journalDataArray[$journalArrayData]['amount'])==trim($jsonDecodedProductData[0]->tax))
					{
						$taxFlag=1;
					}
				}
				if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
				{
					//discount ledger exist
					$journalDiscountFlag=1;
					$discount=0;
					
					for($productArrayData=0;$productArrayData<count($jsonDecodedProductData);$productArrayData++)
					{
						if(strcmp($jsonDecodedProductData[$productArrayData]->discountType,"flat")==0)
						{
							$discountArray[$productArrayData] = $jsonDecodedProductData[$productArrayData]->discount;
						}
						else
						{
							$discountArray[$productArrayData] = ($jsonDecodedProductData[$productArrayData]->discount/100)*$jsonDecodedProductData[$productArrayData]->price;
						}
						$discount = $discount+$discountArray[$productArrayData];
					}
					if(trim($discount)==trim($journalDataArray[$journalArrayData]['amount']))
					{
						$discountFlag=1;
					}
				}
			}
			else
			{
				if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
				{
					$journalTaxFlag=1;
					//tax  ledger exist
					if(trim($journalDataArray[$journalArrayData]['amount'])==trim($jsonDecodedProductData[0]->tax))
					{
						$taxFlag=1;
					}
				}
				if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
				{
					$discount=0;
					//discount ledger exist
					$journalDiscountFlag=1;
					for($productArrayData=0;$productArrayData<count($jsonDecodedProductData);$productArrayData++)
					{
						if(strcmp($jsonDecodedProductData[$productArrayData]->discountType,"flat")==0)
						{
							$discountArray[$productArrayData] = $jsonDecodedProductData[$productArrayData]->discount;
						}
						else
						{
							$discountArray[$productArrayData] = ($jsonDecodedProductData[$productArrayData]->discount/100)*$jsonDecodedProductData[$productArrayData]->price;
						}
						$discount = $discount+$discountArray[$productArrayData];
					}
					if(trim($discount)==trim($journalDataArray[$journalArrayData]['amount']))
					{
						$discountFlag=1;
					}
				}
			}
		}
		if($journalTaxFlag==1 && $taxFlag==0 || $journalDiscountFlag==1 && $discountFlag==0)
		{
			return $exceptionArray['content'];
		}
		return $journalData;
	}
	
	/**
	 * validate jouranl-product data for update/insert
     * @param trim-array of journal and product header data
	 * check journal and product data and if tax and discount is available then check that value
     * @return array/exception message
     */
	public function validateInsertBuisnessLogic($productData,$journalData,$journalType)
	{
		$taxFlag=0;
		$journalTaxFlag=0;
		$journalDiscountFlag=0;
		$discountFlag=0;
		$journalCommissionFlag = 0;
		$commissionFlag = 0;
		$discountJournalFlag=0;
		$discountArray = array();
		$ledgerService = new LedgerService();
		$ledgerArrayConst = new LedgerArray();
		$ledgerArrayData = $ledgerArrayConst->commissionLedgerArray();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		for($journalArrayData=0;$journalArrayData<count($journalData[0]);$journalArrayData++)
		{
			$ledgerIdArray[$journalArrayData] = $journalData[0][$journalArrayData]['ledgerId'];
			$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
			if(strcmp("sales",$journalType)==0)
			{
				if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==18)
				{
					$journalTaxFlag=1;
					//tax ledger exist
					if(trim($journalData[0][$journalArrayData]['amount'])==trim($productData['tax']))
					{
						$taxFlag=1;
					}
				}
				if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
				{
					//discount ledger exist
					$journalCommissionFlag = 1;
					
					if (strcmp($ledgerArrayData[0], json_decode($ledgerResult)->ledgerName) == 0) {
						$commissionFlag = 1;
					}else{
						$discount=0;
						$journalDiscountFlag=1;
						//calculate total discount in product
						for($productArrayData=0;$productArrayData<count($productData[0]);$productArrayData++)
						{
							$discount = $discount+$productData[0][$productArrayData]['discountValue'];
						} 
						if(trim($discount)==trim($journalData[0][$journalArrayData]['amount']))
						{
							$discountFlag=1;
						}
					}
				}
			}
			else
			{
				if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
				{
					$journalTaxFlag=1;
					//tax ledger exist
					if(trim($journalData[0][$journalArrayData]['amount'])==trim($productData['tax']))
					{
						$taxFlag=1;
					}
				}
				if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
				{
					//discount ledger exist
					$discount=0;
					//calculate total discount in product
					for($productArrayData=0;$productArrayData<count($productData[0]);$productArrayData++)
					{
						$discount = $discount+$productData[0][$productArrayData]['discountValue'];
					}
					if(trim($discount)==trim($journalData[0][$journalArrayData]['amount']))
					{
						$discountFlag=1;
					}
				}
			}
		}
		if($journalTaxFlag==1 && $taxFlag==0 || $journalDiscountFlag==1 && $discountFlag==0 && $journalCommissionFlag ==1 && $commissionFlag==0)
		{
			return $exceptionArray['content'];
		}
		
		//reverse checking(product to journal checking)
		if($productData['tax']==0)
		{
			$discount=0;
			for($productArrayData=0;$productArrayData<count($productData[0]);$productArrayData++)
			{
				$discount = $discount+$productData[0][$productArrayData]['discountValue'];
			}
			if($discount!=0)
			{
				for($journalInnerArrayData=0;$journalInnerArrayData<count($journalData[0]);$journalInnerArrayData++)
				{
					$ledgerIdArray[$journalInnerArrayData] = $journalData[0][$journalInnerArrayData]['ledgerId'];
					$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalInnerArrayData]);
					if(strcmp("sales",$journalType)==0)
					{
						if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
						{
							$journalDiscountFlag=1;
							if(trim($discount)==trim($journalData[0][$journalInnerArrayData]['amount']))
							{
								$discountJournalFlag=1;
							}
						}
					}
					else
					{
						if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
						{
							$journalDiscountFlag=1;
							if(trim($discount)==trim($journalData[0][$journalInnerArrayData]['amount']))
							{
								$discountJournalFlag=1;
							}
						}
					}
				}
				if($journalDiscountFlag==0 || $discountJournalFlag==0)
				{
					return $exceptionArray['content'];
				}
			}
		}
		else
		{
			//tax ledger should be exist in journal
			for($journalArrayData=0;$journalArrayData<count($journalData[0]);$journalArrayData++)
			{
				$ledgerIdArray[$journalArrayData] = $journalData[0][$journalArrayData]['ledgerId'];
				$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalArrayData]);
				if(strcmp("sales",$journalType)==0)
				{
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==18)
					{
						//tax exist
						$journalTaxFlag=1;
						if(trim($journalData[0][$journalArrayData]['amount'])==trim($productData['tax']))
						{
							$taxFlag=1;
						}
					}
				}
				else
				{
					if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
					{
						//tax exist
						$journalTaxFlag=1;
						if(trim($journalData[0][$journalArrayData]['amount'])==trim($productData['tax']))
						{
							$taxFlag=1;
						}
					}
					
				}
			}
			if($taxFlag==0 || $journalTaxFlag==0)
			{
				return $exceptionArray['content'];
			}
			
			$discount=0;
			for($productArrayData=0;$productArrayData<count($productData[0]);$productArrayData++)
			{
				$discount = $discount+$productData[0][$productArrayData]['discountValue'];
			}
			if($discount!=0)
			{
				for($journalInnerArrayData=0;$journalInnerArrayData<count($journalData[0]);$journalInnerArrayData++)
				{
					$ledgerIdArray[$journalInnerArrayData] = $journalData[0][$journalInnerArrayData]['ledgerId'];
					$ledgerResult = $ledgerService->getLedgerData($ledgerIdArray[$journalInnerArrayData]);
					if(strcmp("sales",$journalType)==0)
					{
						if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==16)
						{
							$journalDiscountFlag=1;
							if(trim($discount)==trim($journalData[0][$journalInnerArrayData]['amount']))
							{
								$discountJournalFlag=1;
							}
						}
					}
					else
					{
						if(json_decode($ledgerResult)->ledgerGroup->ledgerGroupId==17)
						{
							$journalDiscountFlag=1;
							if(trim($discount)==trim($journalData[0][$journalInnerArrayData]['amount']))
							{
								$discountJournalFlag=1;
							}
						}
					}
				}
				if($journalDiscountFlag==0 || $discountJournalFlag==0)
				{
					return $exceptionArray['content'];
				}
			}
		}
		return $journalData;
	}
}