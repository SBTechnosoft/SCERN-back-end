<?php
namespace ERP\Model\Settings;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Accounting\Journals\JournalModel;
use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Core\Accounting\Ledgers\Entities\EncodeData;
use TCPDFBarcode;
use ERP\Model\Products\ProductModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class SettingModel extends Model
{
	protected $table = 'setting_mst';
	
	/**
	 * insert data 
	 * @param  array
	 * returns the status
	*/
	public function insertData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$getSettingData = array();
		$getSettingKey = array();
		$getSettingData = func_get_arg(0);
		$getSettingKey = func_get_arg(1);
		
		$barcodeArray = array();
		$chequeNoArray = array();
		$serviceDateArray = array();
		$serviceDateArray = array();
		$productArray = array();
		$clientArray = array();
		$billArray = array();
		$advanceBillArray = array();
		$webIntegrationArray = array();
		$inventoryArray = array();
		$barcodeFlag=0;
		$chequeNoFlag=0;
		$serviceDateFlag=0;
		$birthDateFlag=0;
		$anniDateFlag=0;
		$paymaneDateFlag=0;
		$productFlag=0;
		$clientFlag=0;
		$billFlag=0;
		$advanceBillFlag=0;
		$webIntegrationnFlag=0;
		$inventoryFlag=0;
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$constantArray = $constantDatabase->constantVariable();
		
		for($data=0;$data<count($getSettingData);$data++)
		{
			$explodedSetting = explode('_',$getSettingKey[$data]);
			if(strcmp($constantArray['barcodeSetting'],$explodedSetting[0])==0)
			{
				$barcodeFlag=1;
				$barcodeArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['chequeNoSetting'],$explodedSetting[0])==0)
			{
				$chequeNoFlag=1;
				$chequeNoArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['serviceDateSetting'],$explodedSetting[0])==0)
			{
				$serviceDateFlag=1;
				$serviceDateArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['birthDateReminderSetting'],$explodedSetting[0])==0)
			{
				$birthDateFlag=1;
				$birthDateArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['anniDateReminderSetting'],$explodedSetting[0])==0)
			{
				$anniDateFlag=1;
				$anniDateArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['paymentDateSetting'],$explodedSetting[0])==0)
			{
				$paymaneDateFlag=1;
				$paymaneDateArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['productSetting'],$explodedSetting[0])==0)
			{
				$productFlag=1;
				$productArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['clientSetting'],$explodedSetting[0])==0)
			{
				$clientFlag=1;
				$clientArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['billSetting'],$explodedSetting[0])==0)
			{
				$billFlag=1;
				$billArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['advanceBillSetting'],$explodedSetting[0])==0)
			{
				$advanceBillFlag=1;
				$advanceBillArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['webIntegrationSetting'],$explodedSetting[0])==0)
			{
				$webIntegrationnFlag=1;
				$webIntegrationArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
			else if(strcmp($constantArray['inventorySetting'],$explodedSetting[0])==0)
			{
				$inventoryFlag=1;
				$inventoryArray[$getSettingKey[$data]] = $getSettingData[$data];
			}
		}


		if($barcodeFlag==1)
		{
			$decodedSettingData = array();
			$settingType = $constantArray['barcodeSetting'];
			//get setting data
			$settingData = $this->getParticularTypeData($settingType);
			$decodedSettingData = json_decode($settingData);
			if(count($decodedSettingData)!=0)
			{
				if(strcmp($decodedSettingData[0]->setting_type,$constantArray['barcodeSetting'])==0)
				{
					return $exceptionArray['content'];
				}
			}
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['barcodeSetting']."','".json_encode($barcodeArray)."','".$mytime."')");
			DB::commit();
		}
		else if($chequeNoFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['chequeNoSetting']."','".json_encode($chequeNoArray)."','".$mytime."')");
			DB::commit();
		}
		else if($serviceDateFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['serviceDateSetting']."','".json_encode($serviceDateArray)."','".$mytime."')");
			DB::commit();
		}
		else if($paymaneDateFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['paymentDateSetting']."','".json_encode($paymaneDateArray)."','".$mytime."')");
			DB::commit();
		}
		else if($birthDateFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['birthDateReminderSetting']."','".json_encode($birthDateArray)."','".$mytime."')");
			DB::commit();
		}
		else if($anniDateFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['anniDateReminderSetting']."','".json_encode($anniDateArray)."','".$mytime."')");
			DB::commit();
		}
		else if($productFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['productSetting']."','".json_encode($productArray)."','".$mytime."')");
			DB::commit();
		}
		else if($clientFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['clientSetting']."','".json_encode($clientArray)."','".$mytime."')");
			DB::commit();
		}
		else if($billFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['billSetting']."','".json_encode($billArray)."','".$mytime."')");
			DB::commit();
		}
		else if($advanceBillFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['advanceBillSetting']."','".json_encode($advanceBillArray)."','".$mytime."')");
			DB::commit();
		}
		else if($webIntegrationnFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['webIntegrationSetting']."','".json_encode($webIntegrationArray)."','".$mytime."')");
			DB::commit();
		}
		else if($inventoryFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into setting_mst(setting_type,setting_data,created_at) 
			values('".$constantArray['inventorySetting']."','".json_encode($inventoryArray)."','".$mytime."')");
			DB::commit();
		}

		if($raw==1)
		{
			if($barcodeFlag==1)
			{
				//update barcode of all productdata
				$productBarcodeResult = $this->updateProductBarcode($barcodeArray);
			}
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	public function updateProductBarcode($productBarcodeData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$mytime = Carbon\Carbon::now();	
		$productModal = new ProductModel();
		$productData = $productModal->getAllData();	
		$decodedProductData = json_decode($productData);
		$productCount = count($decodedProductData);
		//get constant array
		$constantArray = $constantDatabase->constantVariable();
		$path = $constantArray['productBarcode'];
		//get all product data
		for($productArray=0;$productArray<$productCount;$productArray++)
		{
			//make unique name of barcode svg image
			$dateTime = date("d-m-Y h-i-s");
			$convertedDateTime = str_replace(" ","-",$dateTime);
			$splitDateTime = explode("-",$convertedDateTime);
			$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
			$documentName = $combineDateTime."n".mt_rand(1,9999)."e".mt_rand(1,9999)."w.svg";
			$documentPath = $path.$documentName;
			// echo "fff";
			echo $decodedProductData[$productArray]->product_code;
			//insert barcode image
			$barcodeobj = new TCPDFBarcode($decodedProductData[$productArray]->product_code, 'C128','C');
			file_put_contents($documentPath,$barcodeobj->getBarcodeSVGcode($productBarcodeData['barcode_width'] ,$productBarcodeData['barcode_height'], 'black'));
			//update document-data into database
			DB::beginTransaction();
			$documentStatus = DB::connection($databaseName)->statement("update
			product_mst set document_name='".$documentName."',updated_at='".$mytime."'
			where deleted_at='0000-00-00 00:00:00' and product_id='".$decodedProductData[$productArray]->product_id."'");
			DB::commit();
		}
		return $exceptionArray['200'];
	}

	/**
	 * update data 
	 * @param  setting-data,key of setting-data
	 * returns the status
	*/
	public function updateData($settingData,$key)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$barcodeArray = array();
		$chequeNoArray = array();
		$serviceDateArray = array();
		$paymentDateArray = array();
		$birthDateArray = array();
		$anniDateArray = array();
		$productArray = array();
		$clientArray = array();
		$billArray = array();
		$advanceBillArray = array();
		$webIntegrationArray = array();
		$inventoryArray = array();
		date_default_timezone_set("Asia/Calcutta");
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		
		$chequeNoFlag=0;
		$serviceDateFlag=0;
		$barcodeFlag=0;
		$paymentDateFlag=0;
		$birthDateFlag=0;
		$anniDateFlag=0;
		$productFlag=0;
		$clientFlag=0;
		$billFlag=0;
		$advanceBillFlag=0;
		$webIntegrationnFlag=0;
		$inventoryFlag=0;

		$constantArray = $constantDatabase->constantVariable();
		for($data=0;$data<count($settingData);$data++)
		{
			$explodedSetting = explode('_',$key[$data]);
			if(strcmp($constantArray['barcodeSetting'],$explodedSetting[0])==0)
			{
				$barcodeFlag=1;
				$barcodeArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['chequeNoSetting'],$explodedSetting[0])==0)
			{
				$chequeNoFlag=1;
				$chequeNoArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['serviceDateSetting'],$explodedSetting[0])==0)
			{
				$serviceDateFlag=1;
				$serviceDateArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['paymentDateSetting'],$explodedSetting[0])==0)
			{
				$paymentDateFlag=1;
				$paymentDateArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['birthDateReminderSetting'],$explodedSetting[0])==0)
			{
				$birthDateFlag=1;
				$birthDateArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['anniDateReminderSetting'],$explodedSetting[0])==0)
			{
				$anniDateFlag=1;
				$anniDateArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['productSetting'],$explodedSetting[0])==0)
			{
				$productFlag=1;
				$productArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['clientSetting'],$explodedSetting[0])==0)
			{
				$clientFlag=1;
				$clientArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['billSetting'],$explodedSetting[0])==0)
			{
				$billFlag=1;
				$billArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['advanceBillSetting'],$explodedSetting[0])==0)
			{
				$advanceBillFlag=1;
				$advanceBillArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['webIntegrationSetting'],$explodedSetting[0])==0)
			{
				$webIntegrationnFlag=1;
				$webIntegrationArray[$key[$data]] = $settingData[$data];
			}
			else if(strcmp($constantArray['inventorySetting'],$explodedSetting[0])==0)
			{
				$inventoryFlag=1;
				$inventoryArray[$key[$data]] = $settingData[$data];
			}
		}
		
		if($barcodeFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($barcodeArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['barcodeSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($chequeNoFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($chequeNoArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['chequeNoSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($serviceDateFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($serviceDateArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['serviceDateSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($paymentDateFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($paymentDateArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['paymentDateSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($birthDateFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($birthDateArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['birthDateReminderSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($anniDateFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($anniDateArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['anniDateReminderSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($productFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($productArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['productSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($clientFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($clientArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['clientSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($billFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($billArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['billSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($advanceBillFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($advanceBillArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['advanceBillSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($webIntegrationnFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($webIntegrationArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['webIntegrationSetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else if($inventoryFlag==1)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			setting_mst 
			set setting_data = '".json_encode($inventoryArray)."',
			updated_at = '".$mytime."'
			where setting_type='".$constantArray['inventorySetting']."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}

		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			if($barcodeFlag==1)
			{
				//update barcode of all productdata
				$productBarcodeResult = $this->updateProductBarcode($barcodeArray);
			}
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * get-all data 
	 * returns error-message/data
	 */
	public function getAllData()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select
		setting_id,
		setting_type,
		setting_data,
		created_at,
		updated_at
		from setting_mst
		where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)!=0)
		{
			return json_encode($raw);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * get-particular setting type data 
	 * returns error-message/data
	 */
	public function getParticularTypeData($type)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select
		setting_id,
		setting_type,
		setting_data,
		created_at,
		updated_at
		from setting_mst
		where setting_type='".$type."' and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)!=0)
		{
			return json_encode($raw);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}

	/**
	 * get remaining payment data
	 * returns error-message/data
	 */
	public function getRemainingPaymentData()
	{
		//database selection
        $database = "";
        $constantDatabase = new ConstantClass();
        $databaseName = $constantDatabase->constantDatabaseForCron();
        $constantArray = $constantDatabase->constantVariable();
        
        date_default_timezone_set("Asia/Calcutta");
        DB::beginTransaction();
        $settingData = DB::connection($databaseName)->select("select
        setting_id,
        setting_type,
        setting_data,
        created_at,
        updated_at
        from setting_mst
        where deleted_at='0000-00-00 00:00:00'");
        DB::commit();
        //if we get settings-data
        if(count($settingData)!=0)
        {
        	$settingDataCount = count($settingData);
            for($settingArrayData=0;$settingArrayData<$settingDataCount;$settingArrayData++)
            {
            	if(strcmp($settingData[$settingArrayData]->setting_type,$constantArray['paymentDateSetting'])==0)
	            {
	                $decodedSettingData = json_decode($settingData[$settingArrayData]->setting_data);
	                if(strcmp($decodedSettingData->paymentdate_status,"on")==0)
	                {
	                	$result = $this->paymentReminder($decodedSettingData,$databaseName);
	                    return $result;
	                }
	            }
            }
        }
	}

	/**
	 * get remaining payment data
	 * returns error-message/data
	 */
	public function paymentReminder($settingData,$databaseName)
	{
		$constantDatabase = new ConstantClass();
        $constantArray = $constantDatabase->constantVariable();
        //get exception message
        $exception = new ExceptionMessage();
        $exceptionArray = $exception->messageArrays();
        $noOfDays = $settingData->paymentdate_no_of_days;
        //get remining amount of clients
        $journalModel = new JournalModel();
        $journalData = $journalModel->getReminingPayment();
        $decodedJsonData = json_decode($journalData);
        $journalCount = count($decodedJsonData);
        if($journalCount!=0)
        {
        	for($arrayData=0;$arrayData<count($decodedJsonData);$arrayData++)
            {
            	if($arrayData<count($decodedJsonData)-1)
                {
                	if($decodedJsonData[$arrayData]->ledger_id==$decodedJsonData[$arrayData+1]->ledger_id)
                    {
                    	if($decodedJsonData[$arrayData]->amount>=$decodedJsonData[$arrayData+1]->amount)
                        {
                            $decodedJsonData[$arrayData]->amount = 
                                        $decodedJsonData[$arrayData]->amount-$decodedJsonData[$arrayData+1]->amount;
                        }
                        else
                        {
                            $decodedJsonData[$arrayData]->amount = 
                                        $decodedJsonData[$arrayData+1]->amount-$decodedJsonData[$arrayData]->amount;
                            $decodedJsonData[$arrayData]->amount_type = $decodedJsonData[$arrayData+1]->amount_type;         
                        }
                        unset($decodedJsonData[$arrayData+1]);
                        $decodedJsonData = array_values($decodedJsonData);
                    }
                }
            }
        }
        $decodedLedgerData = array();
        if(count($decodedJsonData)!=0)
        {
        	$ledgerDecodedData = array();
        	$ledgerModel = new LedgerModel();
            $ledgerCountData = count($decodedJsonData);
            for($ledgerArrayData=0;$ledgerArrayData<$ledgerCountData;$ledgerArrayData++)
            {
            	//get ledger data
                $ledgerData = $ledgerModel->getData($decodedJsonData[$ledgerArrayData]->ledger_id);
                if(strcmp($ledgerData,$exceptionArray['404'])!=0)
				{
					//encode ledger data and add remaining amount in it
					$encoded = new EncodeData();
					$encodeData = $encoded->getEncodedData($ledgerData);
					$decodedLedgerData = json_decode($encodeData);
					$decodedLedgerData->remainingAmount = $decodedJsonData[$ledgerArrayData]->amount;
					$decodedLedgerData->remainingAmountType = $decodedJsonData[$ledgerArrayData]->amount_type;
				}
				$ledgerDecodedData[$ledgerArrayData] = $decodedLedgerData;
            }
        }
        return json_encode($ledgerDecodedData);
	}
}
