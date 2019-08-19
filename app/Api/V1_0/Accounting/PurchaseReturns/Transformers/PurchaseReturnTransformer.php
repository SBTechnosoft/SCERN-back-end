<?php
namespace ERP\Api\V1_0\Accounting\PurchaseReturns\Transformers;

use ERP\Core\Accounting\Bills\Entities\PaymentModeEnum;
use ERP\Core\Products\Entities\EnumClasses\DiscountTypeEnum;
use ERP\Exceptions\ExceptionMessage;
use Illuminate\Http\Request;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class PurchaseReturnTransformer
{
	/**
	 * @param Request Object
	 * @return array/error message
	 */
	public function trimInsertData(Request $request)
	{
		$purchaseBillArray = array();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$dataArray = $request->input();

		$discountTypeEnum = new DiscountTypeEnum();
		$discountTypeEnumArray = $discountTypeEnum->enumArrays();

		$paymentModeEnum = new PaymentModeEnum();
		$paymentModeArray = $paymentModeEnum->enumArrays();

		//date conversation
		if (array_key_exists('transactionDate', $dataArray)) {
			$splitedDate = explode("-", trim($dataArray['transactionDate']));
			$transactionDate = $splitedDate[2] . "-" . $splitedDate[1] . "-" . $splitedDate[0];
			$purchaseBillArray['transactionDate'] = $transactionDate;
		}
		//date conversation
		if (array_key_exists('entryDate', $dataArray)) {
			$splitedDate = explode("-", trim($dataArray['entryDate']));
			$entryDate = $splitedDate[2] . "-" . $splitedDate[1] . "-" . $splitedDate[0];
			$purchaseBillArray['entryDate'] = $entryDate;
		}
		//Due date conversation
		if (array_key_exists('dueDate', $dataArray)) {
			$splitedDate = explode("-", trim($dataArray['dueDate']));
			$dueDate = $splitedDate[2] . "-" . $splitedDate[1] . "-" . $splitedDate[0];
			$purchaseBillArray['dueDate'] = $dueDate;
		}

		$purchaseBillArray['vendorId'] = array_key_exists('vendorId', $dataArray) ? trim($dataArray['vendorId']) : 0;
		$purchaseBillArray['companyId'] = array_key_exists('companyId', $dataArray) ? trim($dataArray['companyId']) : 0;
		$purchaseBillArray['billNumber'] = array_key_exists('billNumber', $dataArray) ? trim($dataArray['billNumber']) : '';
		$purchaseBillArray['total'] = array_key_exists('total', $dataArray) ? $this->checkValue(trim($dataArray['total'])) : 0;
		$purchaseBillArray['tax'] = array_key_exists('tax', $dataArray) ? $this->checkValue(trim($dataArray['tax'])) : 0;
		$purchaseBillArray['grandTotal'] = array_key_exists('grandTotal', $dataArray) ? $this->checkValue(trim($dataArray['grandTotal'])) : 0;
		$purchaseBillArray['advance'] = array_key_exists('advance', $dataArray) ? $this->checkValue(trim($dataArray['advance'])) : 0;
		$purchaseBillArray['balance'] = array_key_exists('balance', $dataArray) ? $this->checkValue(trim($dataArray['balance'])) : 0;
		$purchaseBillArray['extraCharge'] = array_key_exists('extraCharge', $dataArray) ? $this->checkValue(trim($dataArray['extraCharge'])) : 0;
		$purchaseBillArray['bankName'] = array_key_exists('bankName', $dataArray) ? trim($dataArray['bankName']) : '';
		$purchaseBillArray['checkNumber'] = array_key_exists('checkNumber', $dataArray) ? trim($dataArray['checkNumber']) : '';
		$purchaseBillArray['bankLedgerId'] = array_key_exists('bankLedgerId', $dataArray) ? trim($dataArray['bankLedgerId']) : 0;
		$purchaseBillArray['remark'] = array_key_exists('remark', $dataArray) ? trim($dataArray['remark']) : '';
		$purchaseBillArray['totalDiscount'] = array_key_exists('totalDiscount', $dataArray) ? trim($dataArray['totalDiscount']) : 0;
		$purchaseBillArray['expense'] = array_key_exists('expense', $dataArray) ? json_encode($dataArray['expense']) : "";

		if(array_key_exists('totalDiscounttype',$request->input()))
		{
			if(strcmp(trim($dataArray['totalDiscounttype']),$discountTypeEnumArray['flatType'])==0 || strcmp(trim($dataArray['totalDiscounttype']),$discountTypeEnumArray['percentageType'])==0)
			{
				$purchaseBillArray['totalDiscounttype'] = trim($dataArray['totalDiscounttype']);
			}
			else
			{
				return $exceptionArray['content'];
			}
		}

		$purchaseBillArray['totalCgstPercentage'] = array_key_exists('totalCgstPercentage',$request->input())? trim($dataArray['totalCgstPercentage']):0;
		$purchaseBillArray['totalSgstPercentage'] = array_key_exists('totalSgstPercentage',$dataArray)? trim($dataArray['totalSgstPercentage']):0;
		$purchaseBillArray['totalIgstPercentage'] = array_key_exists('totalIgstPercentage',$dataArray)? trim($dataArray['totalIgstPercentage']):0;

		if(array_key_exists('paymentMode',$request->input()))
		{
			if(strcmp(trim($dataArray['paymentMode']),$paymentModeArray['cashPayment'])==0 || strcmp(trim($dataArray['paymentMode']),$paymentModeArray['bankPayment'])==0|| strcmp(trim($dataArray['paymentMode']),$paymentModeArray['cardPayment'])==0)
			{
				$purchaseBillArray['paymentMode'] = trim($dataArray['paymentMode']);
			}
			else
			{
				return $exceptionArray['content'];
			}
		}

		if(array_key_exists('inventory',$dataArray))
		{
			$inventoryCount = count($dataArray['inventory']);
			$inventoryData = array();
			for($inventoryArray=0;$inventoryArray<$inventoryCount;$inventoryArray++)
			{
				$purchaseBillArray['inventory'][$inventoryArray] = $inventoryData[$inventoryArray] = $dataArray['inventory'][$inventoryArray];
				$purchaseBillArray['inventory'][$inventoryArray]['productId'] = array_key_exists('productId',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['productId']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['productName'] = array_key_exists('productName',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['productName']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['measurementUnit'] = array_key_exists('measurementUnit',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['measurementUnit']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['discount'] = array_key_exists('discount',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['discount']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['price'] = array_key_exists('price',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['price']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['qty'] = array_key_exists('qty',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['qty']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['measurementUnit'] = array_key_exists('measurementUnit',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['measurementUnit']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['color'] = array_key_exists('color',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['color']) : 'XX';
				$purchaseBillArray['inventory'][$inventoryArray]['frameNo'] = array_key_exists('frameNo',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['frameNo']) : '';
				$purchaseBillArray['inventory'][$inventoryArray]['size'] = array_key_exists('size',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['size']) : 'ZZ';
				$purchaseBillArray['inventory'][$inventoryArray]['amount'] = array_key_exists('amount',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['amount']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['cgstPercentage'] = array_key_exists('cgstPercentage',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['cgstPercentage']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['cgstAmount'] = array_key_exists('cgstAmount',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['cgstAmount']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['sgstPercentage'] = array_key_exists('sgstPercentage',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['sgstPercentage']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['sgstAmount'] = array_key_exists('sgstAmount',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['sgstAmount']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['igstPercentage'] = array_key_exists('igstPercentage',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['igstPercentage']) : 0;
				$purchaseBillArray['inventory'][$inventoryArray]['igstAmount'] = array_key_exists('igstAmount',$inventoryData[$inventoryArray])? trim($inventoryData[$inventoryArray]['igstAmount']) : 0;
				if(array_key_exists('discountType',$inventoryData[$inventoryArray]))
				{
					if(strcmp($inventoryData[$inventoryArray]['discountType'],$discountTypeEnumArray['flatType'])==0 || strcmp($inventoryData[$inventoryArray]['discountType'],$discountTypeEnumArray['percentageType'])==0)
					{
						$purchaseBillArray['inventory'][$inventoryArray]['discountType'] = trim($inventoryData[$inventoryArray]['discountType']);
					}
				}
				if (array_key_exists('itemizeDetail', $inventoryData[$inventoryArray])) {
					$itemizeDtlJson = trim($inventoryData[$inventoryArray]['itemizeDetail']);
					$itemizeDtlArray = json_decode($itemizeDtlJson);
					if (count($itemizeDtlArray) > 0) {
						$itemizeDtlArray = array_map(function($itemizeDtl){
							$returnItemize = [];
							$returnItemize['imei_no'] = trim($itemizeDtl->imei_no);
							$returnItemize['barcode_no'] = trim($itemizeDtl->barcode_no);
							$returnItemize['qty'] = trim($itemizeDtl->qty);
							return $returnItemize;
						}, $itemizeDtlArray);
						$purchaseBillArray['inventory'][$inventoryArray]['itemizeDetail'] = $itemizeDtlArray;
					}
				}
			}
		}
		return $purchaseBillArray;
	}

	/**
	* check value
	* @param integer value
	* @return value/0
	*/
	public function checkValue($value)
	{
		if($value=='' || strcmp($value,'undefined')==0 || is_NaN(floatval($value)) || $value==null)
		{
			return 0;
		}
		return $value;    
	}
}
