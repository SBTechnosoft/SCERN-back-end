<?php
namespace ERP\Core\Accounting\Bills\Entities;

use mPDF;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Accounting\Bills\BillModel;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Products\Services\ProductService;
use ERP\Core\Settings\InvoiceNumbers\Services\InvoiceService;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Container\Container;
use ERP\Api\V1_0\Settings\InvoiceNumbers\Controllers\InvoiceController;
use ERP\Core\Accounting\Bills\Entities\CurrencyToWordConversion;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BillMpdf extends CurrencyToWordConversion
{
	public function mpdfGenerate($templateData,$status)
	{
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$htmlBody = json_decode($templateData)[0]->template_body;
		$decodedBillData = json_decode($status);
		if(is_object($decodedBillData))
		{
			$saleId = $decodedBillData->saleId;		
		}
		else
		{
			$saleId = $decodedBillData[0]->sale_id;
		}
		
		//update invoice data (endAt)
		$decodedArray = json_decode($decodedBillData->productArray);
		$productService = new ProductService();
		$productData = array();
		$decodedData = array();
		$index=1;
		
		$invoiceService = new InvoiceService();	
		$invoiceData = $invoiceService->getLatestInvoiceData($decodedBillData->company->companyId);
		if(strcmp($exceptionArray['204'],$invoiceData)==0)
		{
			return $invoiceData;
		}
		$endAt = json_decode($invoiceData)->endAt;
		$invoiceController = new InvoiceController(new Container());
		$invoiceMethod=$constantArray['postMethod'];
		$invoicePath=$constantArray['invoiceUrl'];
		$invoiceDataArray = array();
		$invoiceDataArray['endAt'] = $endAt+1;
		
		$invoiceRequest = Request::create($invoicePath,$invoiceMethod,$invoiceDataArray);
		$updateResult = $invoiceController->update($invoiceRequest,json_decode($invoiceData)->invoiceId);
		$output="";
		$totalAmount =0;
		$totalVatValue=0;
		$totalAdditionalTax=0;
		$totalQty=0;
		if(strcmp($decodedBillData->salesType,"retail_sales")==0)
		{
			for($productArray=0;$productArray<count($decodedArray->inventory);$productArray++)
			{
				//get product-data
				$productData[$productArray] = $productService->getProductData($decodedArray->inventory[$productArray]->productId);
				$decodedData[$productArray] = json_decode($productData[$productArray]);
				// $retailValue = $decodedData[$productArray]->purchasePrice;
				// if($retailValue=="" || $retailValue==0)
				// {
					// $retailValue=$decodedData[$productArray]->mrp;
					// $decodedData[$productArray]->purchasePrice=$decodedData[$productArray]->mrp;
				// }
				//calculate margin value
				$marginValue[$productArray]=($decodedData[$productArray]->margin/100)*$decodedArray->inventory[$productArray]->price;
				
				// convert amount(round) into their company's selected decimal points
				$marginValue[$productArray] = round($marginValue[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);
				$totalPrice = $decodedArray->inventory[$productArray]->price*$decodedArray->inventory[$productArray]->qty;
				$totalPrice = round($totalPrice,$decodedData[$productArray]->company->noOfDecimalPoints);
				
				if(strcmp($decodedArray->inventory[$productArray]->discountType,"flat")==0)
				{
					$discountValue[$productArray] = $decodedArray->inventory[$productArray]->discount;

				}
				else
				{
					$discountValue[$productArray] = ($decodedArray->inventory[$productArray]->discount/100)*$totalPrice;
				}
				
				$finalVatValue = $totalPrice - $discountValue[$productArray];
				
				//calculate vat value;
				$vatValue[$productArray]=($decodedData[$productArray]->vat/100)*$finalVatValue;
				// convert amount(round) into their company's selected decimal points
				$vatValue[$productArray] = round($vatValue[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);
				
				//calculate additional tax
				$additionalTaxValue[$productArray] = ($decodedData[$productArray]->additionalTax/100)*$finalVatValue;
				
				// convert amount(round) into their company's selected decimal points
				
				$additionalTaxValue[$productArray] = round($additionalTaxValue[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);
				
				$total[$productArray] =($totalPrice)-$discountValue[$productArray]+$vatValue[$productArray];
				
				// convert amount(round) into their company's selected decimal points
				$total[$productArray] = round($total[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);
				$trClose = "</td></tr>";
				if($productArray==0)
				{
					$output =$output.$trClose;
				}
				
				$output =$output."".
					'<tr class="trhw" style="font-family: Calibri; text-align: left; height: 25px; background-color: transparent;">
				   <td class="tg-m36b thsrno" style="font-size: 12px; height: 25px; text-align:center; padding:0 0 0 0;">'.$index.'</td>
				   <td class="tg-m36b theqp" style="font-size: 12px;  height: 25px; padding:0 0 0 0;">'. $decodedData[$productArray]->productName.'</td>
				   <td class="tg-ullm thsrno" style="font-size: 12px;  height: 25px; padding:0 0 0 0;">'. $decodedArray->inventory[$productArray]->color.'</td>
				   <td class="tg-ullm thsrno" style="font-size: 12px;  height: 25px; padding:0 0 0 0;">'. $decodedArray->inventory[$productArray]->frameNo.'</td>
				   <td class="tg-ullm thsrno" style="font-size: 12px;   height: 25px; text-align: center; padding:0 0 0 0;">'. $decodedArray->inventory[$productArray]->qty.'</td>
				   <td class="tg-ullm thsrno" style="font-size: 12px; height: 25px; text-align: center; padding:0 0 0 0;">'. $decodedArray->inventory[$productArray]->price.'</td>
				   <td class="tg-ullm thsrno" style="font-size: 12px;  height: 25px; text-align: center; padding:0 0 0 0;">'. $discountValue[$productArray].'</td>
				   <td class="tg-ullm thamt" style="font-size: 12px;  height: 25px; text-align: center; padding:0 0 0 0;">'. $decodedData[$productArray]->vat.'%</td>
				   <td class="tg-ullm thamt" style="font-size: 12px; height: 25px; text-align: center; padding:0 0 0 0;">'.$vatValue[$productArray].'</td>
				   <td class="tg-ullm thamt" style="font-size: 12px;  height: 25px; text-align: center; padding:0 0 0 0;">'.$decodedData[$productArray]->additionalTax.'</td>
				   <td class="tg-ullm thamt" style="font-size: 12px;   height: 25px; text-align: center; padding:0 0 0 0;">'.$additionalTaxValue[$productArray].'</td>
				   <td class="tg-ullm thamt" style="font-size: 12px;  height: 25px; text-align: center; padding:0 0 0 0;">'.$total[$productArray];
				if($productArray != count($decodedArray->inventory)-1)
				{
					$output = $output.$trClose;
				}

				 $index++;
				 $totalVatValue = $totalVatValue+$vatValue[$productArray];
				 $totalAdditionalTax=$totalAdditionalTax+$additionalTaxValue[$productArray];
				 $totalQty=$totalQty+$decodedArray->inventory[$productArray]->qty;
				 
				 $totalAmount=$totalAmount+$total[$productArray];
				 // convert amount(round) into their company's selected decimal points
				$totalAmount = round($totalAmount,$decodedData[$productArray]->company->noOfDecimalPoints);
			}
		}
		else
		{
			for($productArray=0;$productArray<count($decodedArray->inventory);$productArray++)
			{
				//get product-data
				$productData[$productArray] = $productService->getProductData($decodedArray->inventory[$productArray]->productId);
				$decodedData[$productArray] = json_decode($productData[$productArray]);
				
				$marginPrice[$productArray] = ($decodedData[$productArray]->wholesaleMargin/100)*$decodedArray->inventory[$productArray]->price;
				// convert amount(round) into their company's selected decimal points
				$marginPrice[$productArray] = round($marginPrice[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);
				
				$totalPrice[$productArray] = $decodedArray->inventory[$productArray]->price*$decodedArray->inventory[$productArray]->qty;
				// convert amount(round) into their company's selected decimal points
				$totalPrice[$productArray] = round($totalPrice[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);
				if(strcmp($decodedArray->inventory[$productArray]->discountType,"flat")==0)
				{
					$discountValue[$productArray] = $decodedArray->inventory[$productArray]->discount;
				}
				else
				{
					$discountValue[$productArray] = ($decodedArray->inventory[$productArray]->discount/100)*$totalPrice[$productArray];
				}
				
				$total[$productArray] = $totalPrice[$productArray]-$discountValue[$productArray];
				
				
				//calculate vat value;
				$vatValue[$productArray]=($decodedData[$productArray]->vat/100)*$total[$productArray];
				// convert amount(round) into their company's selected decimal points
				$vatValue[$productArray] = round($vatValue[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);
				
				$total[$productArray] = $total[$productArray]+$vatValue[$productArray];
				// convert amount(round) into their company's selected decimal points
				$total[$productArray] = round($total[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);
				
				//calculate additional tax
				$additionalTaxValue[$productArray] = ($decodedData[$productArray]->additionalTax/100)*$total[$productArray];
				// convert amount(round) into their company's selected decimal points
				$additionalTaxValue[$productArray] = round($additionalTaxValue[$productArray],$decodedData[$productArray]->company->noOfDecimalPoints);				

				$output =$output."".
				'<tr class="trhw" style="font-family: Calibri; height: 50px; background-color: transparent; text-align: left;">
				<td class="tg-m36b thsrno" style="font-size: 12px; text-align: center; height: 50px;"><span style="color: #000000;">'.$index.'</span></td>
				<td class="tg-m36b theqp" style="font-size: 12px;  text-align: center; height: 50px;"><span style="color: #000000;">'. $decodedData[$productArray]->productName.'</span></td>
				<td class="tg-ullm thsrno" style="font-size: 12px;  text-align: center; height: 50px;"><span style="color: #000000;">'. $decodedArray->inventory[$productArray]->color.'</span></td>
				<td class="tg-ullm thsrno" style="font-size: 12px; text-align: center; height: 50px;"><span style="color: #000000;">'. $decodedArray->inventory[$productArray]->frameNo.'</span></td>
				<td class="tg-ullm thsrno" style="font-size: 12px;  text-align: center; height: 50px;"><span style="color: #000000;">'. $decodedArray->inventory[$productArray]->qty.'</span></td>
				<td class="tg-ullm thsrno" style="font-size: 12px; text-align: center; height: 50px;"><span style="color: #000000;">'. $decodedArray->inventory[$productArray]->price.'</span></td>
				<td class="tg-ullm thsrno" style="font-size: 12px; text-align: center; height: 50px;"><span style="color: #000000;">'. $discountValue[$productArray].'</span></td>
				<td class="tg-ullm thamt" style="font-size: 12px; text-align: center; height: 50px;"><span style="color: #000000;">'.$decodedData[$productArray]->vat.'%</span></td>
				<td class="tg-ullm thamt" style="font-size: 12px;  text-align: center; height: 50px;"><span style="color: #000000;">'.$vatValue[$productArray].'</span></td>
				<td class="tg-ullm thamt" style="font-size: 12px; text-align: center; height: 50px;"><span style="color: #000000;">'.$decodedData[$productArray]->additionalTax.'</span></td>
				<td class="tg-ullm thamt" style="font-size: 12px;  text-align: center; height: 50px;"><span style="color: #000000;">'.$additionalTaxValue[$productArray].'</span></td>
				<td class="tg-ullm thamt" style="font-size: 12px;  text-align: center; height: 50px;"><span style="color: #000000;">'.$total[$productArray].'</span></td>
				</tr>';
				 $index++;
				 $totalAmount=$totalAmount+$total[$productArray];
				 $totalAdditionalTax=$totalAdditionalTax+$additionalTaxValue[$productArray]+$vatValue[$productArray];
				 $totalQty=$totalQty+$decodedArray->inventory[$productArray]->qty;
				
				// convert amount(round) into their company's selected decimal points
				$totalAmount = round($totalAmount,$decodedData[$productArray]->company->noOfDecimalPoints);
			}
		}

		//calculation of currecy to word conversion
		$currecyToWordConversion = new BillMpdf();
		$currencyResult = $currecyToWordConversion->conversion($totalAmount);
	
		$address = $decodedBillData->client->address1.",".$decodedBillData->client->address2;
		$billArray = array();
		$billArray['Description']=$output;
		$billArray['ClientName']=$decodedBillData->client->clientName;
		$billArray['Company']=$decodedBillData->company->companyName;
		$billArray['Total']=$totalAmount;
		$billArray['Mobile']=$decodedBillData->client->contactNo;
		$billArray['INVID']=$decodedBillData->invoiceNumber;
		$billArray['CLIENTADD']=$address;
		$billArray['OrderDate']=$decodedBillData->entryDate;
		$billArray['REMAINAMT']=$decodedBillData->balance;
		$billArray['TotalTax']=$totalVatValue+$totalAdditionalTax;
		$billArray['TotalQty']=$totalQty;
		$billArray['TotalInWord']=$currencyResult;
		$billArray['displayNone']='none';

		$mpdf = new mPDF('A4','landscape');
		$mpdf->SetDisplayMode('fullpage');
		foreach($billArray as $key => $value)
		{
			$htmlBody = str_replace('['.$key.']', $value, $htmlBody);
		}
		$mpdf->WriteHTML($htmlBody);
		$path = $constantArray['billUrl'];
		
		//change the name of document-name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		$documentPathName = $path.$documentName;
		$documentFormat="pdf";
		$documentType ="bill";
		
		//insertion bill document data into database
		$billModel = new BillModel();
		$billDocumentStatus = $billModel->billDocumentData($saleId,$documentName,$documentFormat,$documentType);
		
		if(strcmp($exceptionArray['500'],$billDocumentStatus)==0)
		{
			return $billDocumentStatus;
		}
		else
		{
			$mpdf->Output($documentPathName,'F');
			$pathArray = array();
			$pathArray['documentPath'] = $documentPathName;
			return $pathArray;
		}	
	}
}
