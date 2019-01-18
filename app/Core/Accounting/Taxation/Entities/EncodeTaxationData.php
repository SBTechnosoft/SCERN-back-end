<?php

namespace ERP\Core\Accounting\Taxation\Entities;



use ERP\Core\Clients\Services\ClientService;

use ERP\Core\Companies\Services\CompanyService;

use Carbon;

use ERP\Core\Products\Services\ProductService;

use ERP\Model\Accounting\Ledgers\LedgerModel;

use ERP\Core\Accounting\Ledgers\Services\LedgerService;

use ERP\Entities\Constants\ConstantClass;

use ERP\Exceptions\ExceptionMessage;

use PHPExcel;

use PHPExcel_IOFactory;

use PHPExcel_Style_Fill;

use PHPExcel_Style_Alignment;

use PHPExcel_Style_Border;

/**

 *

 * @author Reema Patel<reema.p@siliconbrain.in>

 */

class EncodeTaxationData extends ProductService

{

	/**

	 * convert necessary sale-tax data and generate excel-sheet of that data

	 * returns the array-data/exception message/excel-sheet path

	*/

	public function getEncodedAllData($status,$headerData)
	{
		$constantClass = new ConstantClass();

		$constantArray = $constantClass->constantVariable();

		

		//get exception message

		$exception = new ExceptionMessage();

		$exceptionArray = $exception->messageArrays();

		

		$decodedJson = json_decode($status,true);


		$companyService = new CompanyService();

		$data = array();

		$totalAmount = 0;

		$totalAdditioanalTax = 0;

		$totalGrandTotal = 0;
		

		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)

		{

			$totalTax = 0;
			// echo "<pre>";
			// print_r($decodedJson[$decodedData]);
			// exit;
			$totalCgst = 0;
			$totalSgst = 0;
			$totalIgst = 0;

			$calculateAdditionalTax=0;

			$calculateVat=0;

			$decodedProductArrayData = json_decode($decodedJson[$decodedData]['product_array']);

			$productDataArray = array();

			$inventoryCount = count($decodedProductArrayData->inventory);

			

			for($arrayData=0;$arrayData<$inventoryCount;$arrayData++)
			{
				if($decodedProductArrayData->inventory[$arrayData]->productId != '')
				{
					$productService = new EncodeTaxationData();

					$productData = $productService->getProductData($decodedProductArrayData->inventory[$arrayData]->productId);

					$productDecodedData = json_decode($productData);

					// $vat = ($productDecodedData->purchasePrice/100)*$productDecodedData->vat;
					$vat = $decodedProductArrayData->inventory[$arrayData]->cgstAmount;

					$calculateVat = $calculateVat+$vat;

					$totalCgst = $totalCgst + $vat;

					// $additionalTax = ($productDecodedData->purchasePrice/100)*$productDecodedData->additionalTax;
					$additionalTax = $decodedProductArrayData->inventory[$arrayData]->sgstAmount;

					$calculateAdditionalTax = $calculateAdditionalTax+$additionalTax;

					$totalSgst = $totalSgst + $additionalTax;

					/* IGST */
						$igst = $decodedProductArrayData->inventory[$arrayData]->igstAmount;

						$totalIgst = $totalIgst + $igst;
					/* IGST */

					$productDataArray[$arrayData] = $decodedProductArrayData->inventory[$arrayData];

					$productDataArray[$arrayData]->product = $productDecodedData;
				}
			}

			$total[$decodedData] = $decodedJson[$decodedData]['total'];

			$totalDiscount[$decodedData] = $decodedJson[$decodedData]['total_discount'];

			$tax[$decodedData] = $decodedJson[$decodedData]['tax'];

			$grandTotal[$decodedData] = $decodedJson[$decodedData]['grand_total'];

			$advance[$decodedData] = $decodedJson[$decodedData]['advance'];

			$balance[$decodedData] = $decodedJson[$decodedData]['balance'];

			$refund[$decodedData] = $decodedJson[$decodedData]['refund'];

			$entryDate[$decodedData] = $decodedJson[$decodedData]['entry_date'];

			$clientId[$decodedData] = $decodedJson[$decodedData]['client_id'];

			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];

			

			$calculateGrandTotal[$decodedData] = $total[$decodedData]+$calculateVat+$calculateAdditionalTax;


			$clientService = new ClientService();

			$clientData[$decodedData]  = $clientService->getClientData($clientId[$decodedData]);

			$decodedClientData[$decodedData] = json_decode($clientData[$decodedData]);
			
			// convert amount(round) into their company's selected decimal points

			$companyData[$decodedData] = $companyService->getCompanyData($companyId[$decodedData]);

			$companyDecodedData[$decodedData] = json_decode($companyData[$decodedData]);


			$totalAmount  = $totalAmount+$total[$decodedData];

			$totalTax = $totalTax+$totalCgst+$totalSgst+$totalIgst;

			$totalAdditioanalTax = $totalAdditioanalTax+$calculateAdditionalTax;

			$totalGrandTotal = $totalGrandTotal+$calculateGrandTotal[$decodedData];

			

			$total[$decodedData] = number_format($total[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$totalDiscount[$decodedData] = number_format($totalDiscount[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$tax[$decodedData] = number_format($tax[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$grandTotal[$decodedData] = number_format($grandTotal[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$advance[$decodedData] = number_format($advance[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$balance[$decodedData] = number_format($balance[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$refund[$decodedData] = number_format($refund[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$calculateVat = number_format($calculateVat,$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$calculateAdditionalTax = number_format($calculateAdditionalTax,$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$totalCgst = number_format($totalCgst,$companyDecodedData[$decodedData]->noOfDecimalPoints);
			$totalSgst = number_format($totalSgst,$companyDecodedData[$decodedData]->noOfDecimalPoints);
			$totalIgst = number_format($totalIgst,$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$totalTax = number_format($totalTax,$companyDecodedData[$decodedData]->noOfDecimalPoints);


			//date format conversion

			$convertedEntryDate[$decodedData] = strcmp($entryDate[$decodedData],'0000-00-00 00:00:00')==0

												? "00-00-0000"

												: Carbon\Carbon::createFromFormat('Y-m-d', $entryDate[$decodedData])->format('d-m-Y');	



			$data[$decodedData]= array(

				'invoiceNumber'=>$decodedJson[$decodedData]['invoice_number'],

				'salesType'=>$decodedJson[$decodedData]['sales_type'],

				'total'=>$total[$decodedData],

				'totalDiscount'=>$totalDiscount[$decodedData],

				'totalDiscounttype'=>$decodedJson[$decodedData]['total_discounttype'],

				'totalCgst' => $totalCgst,

				'totalSgst' => $totalSgst,

				'totalIgst' => $totalIgst,

				'tax'=> $totalTax,

				'grandTotal'=>$calculateGrandTotal[$decodedData],

				'advance'=>$advance[$decodedData],

				'balance'=>$balance[$decodedData],

				'refund'=>$refund[$decodedData],

				'entryDate'=>$convertedEntryDate[$decodedData],

				'additionalTax'=>$calculateAdditionalTax,

				'client'=>$decodedClientData[$decodedData],

				'company'=>$companyDecodedData[$decodedData],

				'product'=>$productDataArray

			);

			//get ledger-data from invoice-number

			$ledgerModel = new LedgerModel();

			$ledgerData[$decodedData] = $ledgerModel->getDataAsPerContactNo($companyDecodedData[$decodedData]->companyId,$decodedClientData[$decodedData]->contactNo);

			if(strcmp($ledgerData[$decodedData],$exceptionArray['500'])!=0)

			{

				$decodedLedgerData[$decodedData] = json_decode($ledgerData[$decodedData]);

				$data[$decodedData]['ledger'] = $decodedLedgerData[$decodedData][0];

			}

		}



		if(array_key_exists('operation',$headerData))

		{

			if(strcmp($headerData['operation'][0],'excel')==0)

			{

				$totalAmount = number_format($totalAmount,$companyDecodedData[0]->noOfDecimalPoints);

				$totalTax = number_format($totalTax,$companyDecodedData[0]->noOfDecimalPoints);

				$totalAdditioanalTax = number_format($totalAdditioanalTax,$companyDecodedData[0]->noOfDecimalPoints);

				$totalGrandTotal = number_format($totalGrandTotal,$companyDecodedData[0]->noOfDecimalPoints);

				// generate excel

				$objPHPExcel = new \PHPExcel();

				// Set properties comment

				$objPHPExcel->getProperties()->setCreator("ThinkPHP")

								->setLastModifiedBy("Daniel Schlichtholz")

								->setTitle("Office 2007 XLSX Test Document")

								->setSubject("Office 2007 XLSX Test Document")

								->setDescription("Test doc for Office 2007 XLSX, generated by PHPExcel.")

								->setKeywords("office 2007 openxml php")

								->setCategory("Test result file");

				$objPHPExcel->getActiveSheet()->setTitle('SALETAX');

				

				//heading-start

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,1, '1');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,1, 'FORM 201B');

				

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,2, '2');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,2, 'Tax Invoice Number');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,2, 'Date');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,2, 'Name with RC number of the registered dealer from whom goods purchase');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,2, 'Turnover of purchase of taxable goods');

				

				// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D2:E2');

				// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F2:J2');

				

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,3, '3');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,3, 'Purchase of goods from registered dealer');

		

				// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:C3');

				

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,4, '4');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,4, 'Tax Invoice Number');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,4, 'Date');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,4, 'Name');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,4, 'R.C No');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,4, 'Goods With HSN');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,4, 'Value Of Goods');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,4, 'Tax');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,4, 'Additional Tax');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,4, 'Total');

				//heading-end

				//heading-start

				//$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,1, '1');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,1, 'Summary For B2b(4)');

				
				//Second Row
				//$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,2, '2');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,2, 'No. of Recipients');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,2, '');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,2, 'No. of Invoices');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,2, '');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,2, 'Total Invoice Value');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,2, '');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,2, '');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,2, '');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,2, '');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(10,2, '');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(11,2, 'Total Taxation Value');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(12,2, 'Total Cess');

				//Third Row
				//$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,3, '3');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,3, count($data));
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,3, '');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,3, count($data));

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,3, '');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,3, $totalAmount);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,3, '');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,3, '');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,3, '');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,3, '');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(10,3, '');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(11,3, $totalTax);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(12,3, '0.00');


				//4th Row
				//$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,4, '4');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,4, 'GSTIN/UIN of Recipient');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,4, 'Receiver Name');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,4, 'Invoice Number');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,4, 'Invoice Date');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,4, 'Invoice Value');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,4, 'Place Of Supply');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,4, 'Reverse Charge');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,4, 'Invoice Type');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,4, 'E-Commerce GSTIN');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(10,4, 'Rate');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(11,4, 'Taxable Value');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(12,4, 'Cess Amount');

				//heading-end

				for($dataArray=0;$dataArray<count($data);$dataArray++)

				{

					//$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$dataArray+5,$dataArray+5);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$dataArray+5,$data[$dataArray]['client']->gst);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$dataArray+5,$data[$dataArray]['client']->clientName);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$dataArray+5,$data[$dataArray]['invoiceNumber']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,$dataArray+5,$data[$dataArray]['entryDate']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,$dataArray+5,$data[$dataArray]['total']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,$dataArray+5,'24-Gujarat');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,$dataArray+5,'N');

					//$data[$dataArray]['tax']
					//$data[$dataArray]['additionalTax']
					//$data[$dataArray]['grandTotal']

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,$dataArray+5,'Regular');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,$dataArray+5,'');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(10,$dataArray+5,$data[$dataArray]['tax']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(11,$dataArray+5,$data[$dataArray]['tax']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(12,$dataArray+5,'0.00');

				}

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,count($data)+5,count($data)+5);

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,count($data)+5,'Total');

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,count($data)+5,$totalAmount);

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,count($data)+5,$totalTax);

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,count($data)+5,$totalAdditioanalTax);

				// $objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,count($data)+5,$totalGrandTotal);

				// style for header

				$headerStyleArray = array(

				'font'  => array(

					'bold'  => true,

					'color' => array('rgb' => '#00000'),

					'size'  => 10,

					'name'  => 'Verdana'

				));

				

				// style for Title

				$titleStyleArray = array(

				'font'  => array(

					'bold'  => true,

					'color' => array('rgb' => 'Black'),

					'size'  => 15,

					'name'  => 'Verdana'

				));

				

				// set header style

				$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($headerStyleArray);

				$objPHPExcel->getActiveSheet()->getStyle('B2:M2')->applyFromArray($headerStyleArray);

				//$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($headerStyleArray);

				$objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($headerStyleArray);

				

				// set title style

				// $objPHPExcel->getActiveSheet()->getStyle('B2:J2')->applyFromArray($titleStyleArray);

				

				// make unique name

				$dateTime = date("d-m-Y h-i-s");

				$convertedDateTime = str_replace(" ","-",$dateTime);

				$splitDateTime = explode("-",$convertedDateTime);

				$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];

				$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx

				$path = $constantArray['saleTaxUrl'];

				$documentPathName = $path.$documentName;



				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

				$objWriter->save($documentPathName);

				

				$pathArray = array();

				$pathArray['documentPath'] = $documentPathName;
				
				return $pathArray;

			}

			else

			{

				$jsonEncodedData = json_encode($data);

				return $jsonEncodedData;

			}

		}

		else

		{

			$jsonEncodedData = json_encode($data);

			return $jsonEncodedData;

		}



	}

	

	/**

	 * convert necessary purchase-tax data and generate excel-sheet of that data

	 * returns the array-data/exception message/excel-sheet path

	*/

	public function getPurchaseTaxEncodedAllData($status,$headerData)

	{

		$constantClass = new ConstantClass();

		$constantArray = $constantClass->constantVariable();

		

		//get exception message

		$exception = new ExceptionMessage();

		$exceptionArray = $exception->messageArrays();

		

		$decodedJson = json_decode($status,true);

		$companyService = new CompanyService();

		$data = array();

		$totalAmount = 0;

		

		$totalAdditioanalTax = 0;

		$totalGrandTotal = 0;

		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)

		{
			$totalTax = 0;

			$totalCgst = 0;
			$totalSgst = 0;
			$totalIgst = 0;

			$calculateAdditionalTax=0;

			$calculateVat=0;

			$productDataArray = array();

			$decodedProductArrayData = json_decode($decodedJson[$decodedData]['product_array']);

			for($arrayData=0;$arrayData<count($decodedProductArrayData->inventory);$arrayData++)

			{

				$productService = new EncodeTaxationData();

				$productData = $productService->getProductData($decodedProductArrayData->inventory[$arrayData]->productId);

				$productDecodedData = json_decode($productData);

				

				$vat = ($productDecodedData->purchasePrice/100)*$productDecodedData->vat;

				$calculateVat = $calculateVat+$vat;

				$totalCgst = $totalCgst + $decodedProductArrayData->inventory[$arrayData]->cgstAmount;

				$additionalTax = ($productDecodedData->purchasePrice/100)*$productDecodedData->additionalTax;

				$calculateAdditionalTax = $calculateAdditionalTax+$additionalTax;

				$totalSgst = $totalSgst + $decodedProductArrayData->inventory[$arrayData]->sgstAmount;

				$totalIgst = $totalIgst + $decodedProductArrayData->inventory[$arrayData]->igstAmount;

				$productDataArray[$arrayData] = $decodedProductArrayData->inventory[$arrayData];

				$productDataArray[$arrayData]->product = $productDecodedData;

			}

			$total[$decodedData] = $decodedJson[$decodedData]['total'];

			$totalDiscount[$decodedData] = $decodedJson[$decodedData]['total_discount'];

			$tax[$decodedData] = $decodedJson[$decodedData]['tax'];

			$grandTotal[$decodedData] = $decodedJson[$decodedData]['grand_total'];

			$transactionDate[$decodedData] = $decodedJson[$decodedData]['transaction_date'];

			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];

			$calculateGrandTotal[$decodedData] = $total[$decodedData]+$calculateVat+$calculateAdditionalTax;

			// convert amount(round) into their company's selected decimal points

			$companyData[$decodedData] = $companyService->getCompanyData($companyId[$decodedData]);

			$companyDecodedData[$decodedData] = json_decode($companyData[$decodedData]);

			

			$totalAmount  = $totalAmount+$total[$decodedData];

			// $totalTax = $totalTax+$calculateVat;
			$totalTax = $totalTax+$totalCgst+$totalSgst+$totalIgst;

			$totalAdditioanalTax = $totalAdditioanalTax+$calculateAdditionalTax;

			$totalGrandTotal = $totalGrandTotal+$calculateGrandTotal[$decodedData];

			

			$total[$decodedData] = number_format($total[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$totalDiscount[$decodedData] = number_format($totalDiscount[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$tax[$decodedData] = number_format($tax[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$grandTotal[$decodedData] = number_format($grandTotal[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$calculateVat = number_format($calculateVat,$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$calculateAdditionalTax = number_format($calculateAdditionalTax,$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$totalCgst = number_format($totalCgst,$companyDecodedData[$decodedData]->noOfDecimalPoints);
			$totalSgst = number_format($totalSgst,$companyDecodedData[$decodedData]->noOfDecimalPoints);
			$totalIgst = number_format($totalIgst,$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$totalTax = number_format($totalTax,$companyDecodedData[$decodedData]->noOfDecimalPoints);

			// date format conversion
			if(strcmp($transactionDate[$decodedData],'0000-00-00')==0)
			{

				$convertedTransactionDate[$decodedData] = "00-00-0000";

			}

			else

			{

				$convertedTransactionDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $transactionDate[$decodedData])->format('d-m-Y');

			}

			//get ledger-data from invoice-number

			$ledgerService = new LedgerService();

			$ledgerData = $ledgerService->getLedgerData($decodedJson[$decodedData]['vendor_id']);

			if(strcmp($ledgerData,$exceptionArray['404'])!=0)

			{

				$decodedLedgerData[$decodedData] = json_decode($ledgerData);

				// $data[$decodedData]['ledger'] = $decodedLedgerData[$decodedData];

			}

			else

			{

				return $exceptionArray['404'];

			}

			$data[$decodedData]= array(

				'billNumber'=>$decodedJson[$decodedData]['bill_number'],

				'entryDate'=>$decodedJson[$decodedData]['entry_date'],

				'transactionType'=>$decodedJson[$decodedData]['transaction_type'],

				'total'=>$total[$decodedData],

				'totalDiscount'=>$totalDiscount[$decodedData],

				'totalDiscounttype'=>$decodedJson[$decodedData]['total_discounttype'],

				'totalCgst' => $totalCgst,

				'totalSgst' => $totalSgst,

				'totalIgst' => $totalIgst,

				'totalTax' => $totalTax,

				'tax'=> $totalTax,

				'grandTotal'=>$calculateGrandTotal[$decodedData],

				'transactionDate'=>$convertedTransactionDate[$decodedData],

				'additionalTax'=>$calculateAdditionalTax,

				'ledger'=>$decodedLedgerData[$decodedData],

				'company'=>$companyDecodedData[$decodedData],

				'product'=>$productDataArray

			);

		}

		

		if(array_key_exists('operation',$headerData))

		{

			if(strcmp($headerData['operation'][0],'excel')==0)

			{

				$totalAmount = number_format($totalAmount,$companyDecodedData[0]->noOfDecimalPoints);

				$totalTax = number_format($totalTax,$companyDecodedData[0]->noOfDecimalPoints);

				$totalAdditioanalTax = number_format($totalAdditioanalTax,$companyDecodedData[0]->noOfDecimalPoints);

				$totalGrandTotal = number_format($totalGrandTotal,$companyDecodedData[0]->noOfDecimalPoints);

			

				// generate excel

				$objPHPExcel = new \PHPExcel();

				// Set properties comment

				$objPHPExcel->getProperties()->setCreator("ThinkPHP")

								->setLastModifiedBy("Daniel Schlichtholz")

								->setTitle("Office 2007 XLSX Test Document")

								->setSubject("Office 2007 XLSX Test Document")

								->setDescription("Test doc for Office 2007 XLSX, generated by PHPExcel.")

								->setKeywords("office 2007 openxml php")

								->setCategory("Test result file");

				$objPHPExcel->getActiveSheet()->setTitle('PURCHASETAX');

				

				//heading-start

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,1, '1');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,1, 'FORM 201B');

				

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,2, '2');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,2, 'Tax Invoice Number');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,2, 'Date');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,2, 'Name with RC number of the registered dealer from whom goods purchase');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,2, 'Turnover of purchase of taxable goods');

				

				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('D2:E2');

				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F2:J2');

				

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,3, '3');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,3, 'Purchase of goods from registered dealer');

		

				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:C3');

				

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,4, '4');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,4, 'Tax Invoice Number');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,4, 'Date');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,4, 'Name');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,4, 'R.C No');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,4, 'Goods With HSN');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,4, 'Value Of Goods');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,4, 'Tax');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,4, 'Additional Tax');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,4, 'Total');

				//heading-end

				

				for($dataArray=0;$dataArray<count($data);$dataArray++)

				{

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$dataArray+5,$dataArray+5);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$dataArray+5,$data[$dataArray]['billNumber']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$dataArray+5,$data[$dataArray]['transactionDate']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$dataArray+5,$data[$dataArray]['clientName']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,$dataArray+5,'');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,$dataArray+5,'');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,$dataArray+5,$data[$dataArray]['total']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,$dataArray+5,$data[$dataArray]['tax']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,$dataArray+5,$data[$dataArray]['additionalTax']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,$dataArray+5,$data[$dataArray]['grandTotal']);

				}

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,count($data)+5,count($data)+5);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,count($data)+5,'Total');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,count($data)+5,$totalAmount);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,count($data)+5,$totalTax);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,count($data)+5,$totalAdditioanalTax);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,count($data)+5,$totalGrandTotal);

				// style for header

				$headerStyleArray = array(

				'font'  => array(

					'bold'  => true,

					'color' => array('rgb' => '#00000'),

					'size'  => 10,

					'name'  => 'Verdana'

				));

				

				// style for Title

				$titleStyleArray = array(

				'font'  => array(

					'bold'  => true,

					'color' => array('rgb' => 'Black'),

					'size'  => 15,

					'name'  => 'Verdana'

				));

				

				// set header style

				$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($headerStyleArray);

				$objPHPExcel->getActiveSheet()->getStyle('B2:J2')->applyFromArray($headerStyleArray);

				$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($headerStyleArray);

				$objPHPExcel->getActiveSheet()->getStyle('B4:J4')->applyFromArray($headerStyleArray);

				

				// set title style

				// $objPHPExcel->getActiveSheet()->getStyle('B2:J2')->applyFromArray($titleStyleArray);

				

				// make unique name

				$dateTime = date("d-m-Y h-i-s");

				$convertedDateTime = str_replace(" ","-",$dateTime);

				$splitDateTime = explode("-",$convertedDateTime);

				$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];

				$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx

				$path = $constantArray['purchaseTaxUrl'];

				$documentPathName = $path.$documentName;

				

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

				$objWriter->save($documentPathName);

				

				$pathArray = array();

				$pathArray['documentPath'] = $documentPathName;

				return $pathArray;

			}

			else

			{

				$jsonEncodedData = json_encode($data);

				return $jsonEncodedData;

			}

		}

		else

		{

			$jsonEncodedData = json_encode($data);

			return $jsonEncodedData;

		}

	}

	

	/**

	 * convert necessary purchase-detail data and generate excel-sheet of that data

	 * returns the array-data/exception message/excel-sheet path

	*/

	public function getPurchaseEncodedAllData($status,$headerData)

	{
		
		$constantClass = new ConstantClass();

		$constantArray = $constantClass->constantVariable();

		

		$decodedJson = json_decode($status,true);

		$companyService = new CompanyService();

		$data = array();

		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)

		{

			$calculateAdditionalTax=0;

			$decodedProductArrayData = json_decode($decodedJson[$decodedData]['product_array']);

			for($arrayData=0;$arrayData<count($decodedProductArrayData->inventory);$arrayData++)

			{

				$productService = new EncodeTaxationData();

				$productData = $productService->getProductData($decodedProductArrayData->inventory[$arrayData]->productId);

				$productDecodedData = json_decode($productData);

				$additionalTax = ($productDecodedData->purchasePrice/100)*$productDecodedData->additionalTax;

				$calculateAdditionalTax = $calculateAdditionalTax+$additionalTax;

			}

			$total[$decodedData] = $decodedJson[$decodedData]['total'];

			$tax[$decodedData] = $decodedJson[$decodedData]['tax'];

			$grandTotal[$decodedData] = $decodedJson[$decodedData]['grand_total'];

			$transactionDate[$decodedData] = $decodedJson[$decodedData]['transaction_date'];

			$clientName[$decodedData] = $decodedJson[$decodedData]['client_name'];

			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];

			

			// convert amount(round) into their company's selected decimal points

			$companyData[$decodedData] = $companyService->getCompanyData($companyId[$decodedData]);

			$companyDecodedData[$decodedData] = json_decode($companyData[$decodedData]);

				

			$total[$decodedData] = number_format($total[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$tax[$decodedData] = number_format($tax[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			$grandTotal[$decodedData] = number_format($grandTotal[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);

			

			// date format conversion

			if(strcmp($transactionDate[$decodedData],'0000-00-00 00:00:00')==0)

			{

				$convertedTransactionDate[$decodedData] = "00-00-0000";

			}

			else

			{

				$convertedTransactionDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $transactionDate[$decodedData])->format('d-m-Y');

			}

			$data[$decodedData]= array(

				'billNumber'=>$decodedJson[$decodedData]['bill_number'],

				'transactionType'=>$decodedJson[$decodedData]['transaction_type'],

				'total'=>$total[$decodedData],

				'grandTotal'=>$grandTotal[$decodedData],

				'transactionDate'=>$convertedTransactionDate[$decodedData],

				'clientName'=>$clientName[$decodedData],

				'tax'=>$tax[$decodedData]

			);

		}

		if(array_key_exists('operation',$headerData))

		{

			if(strcmp($headerData['operation'][0],'excel')==0)

			{

				// generate excel

				$objPHPExcel = new \PHPExcel();

				// Set properties comment

				$objPHPExcel->getProperties()->setCreator("ThinkPHP")

								->setLastModifiedBy("Daniel Schlichtholz")

								->setTitle("Office 2007 XLSX Test Document")

								->setSubject("Office 2007 XLSX Test Document")

								->setDescription("Test doc for Office 2007 XLSX, generated by PHPExcel.")

								->setKeywords("office 2007 openxml php")

								->setCategory("Test result file");

				$objPHPExcel->getActiveSheet()->setTitle('PURCHASEDETAILS');

				

				//heading-start

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,1, '1');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,1, 'PURCHASE DETAILS');

				

				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B1:K1');

				

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,2, '2');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,2, 'Invoice Number');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,2, 'Invoice Date');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,2, 'Seller Tin No.');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,2, 'Seller Name');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,2, 'State');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,2, 'Goods With HSN');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,2, 'Value Of Goods');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,2, 'Tax');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,2, 'Total');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(10,2, 'Form Type');

				//heading-end

				

				for($dataArray=0;$dataArray<count($data);$dataArray++)

				{

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$dataArray+3,$dataArray+3);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$dataArray+3,$data[$dataArray]['billNumber']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$dataArray+3,$data[$dataArray]['transactionDate']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$dataArray+3,'');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,$dataArray+3,$data[$dataArray]['clientName']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,$dataArray+3,'');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,$dataArray+3,'');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,$dataArray+3,'');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,$dataArray+3,$data[$dataArray]['tax']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,$dataArray+3,$data[$dataArray]['total']);

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(10,$dataArray+3,'');

				}

				

				// style for header

				$headerStyleArray = array(

				'font'  => array(

					'bold'  => true,

					'color' => array('rgb' => '#00000'),

					'size'  => 10,

					'name'  => 'Verdana'

				));

				

				// style for Title

				$titleStyleArray = array(

				'font'  => array(

					'bold'  => true,

					'color' => array('rgb' => 'Black'),

					'size'  => 15,

					'name'  => 'Verdana'

				));

				

				// set header style

				$objPHPExcel->getActiveSheet()->getStyle('B2:K2')->applyFromArray($headerStyleArray);

				$objPHPExcel->getActiveSheet()->getStyle('B1:K1')->applyFromArray($headerStyleArray);

				// set title style

				// $objPHPExcel->getActiveSheet()->getStyle('B1:K1')->applyFromArray($titleStyleArray);

				

				// make unique name

				$dateTime = date("d-m-Y h-i-s");

				$convertedDateTime = str_replace(" ","-",$dateTime);

				$splitDateTime = explode("-",$convertedDateTime);

				$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];

				$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx

				$path = $constantArray['purchaseTaxationUrl'];

				$documentPathName = $path.$documentName;

				

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

				$objWriter->save($documentPathName);

				

				$pathArray = array();

				$pathArray['documentPath'] = $documentPathName;

				return $pathArray;

			}

		}

		else

		{

			$jsonEncodedData = json_encode($data);

			return $jsonEncodedData;

		}

	}



	/**

	 * generate excel-sheet of gst-return data

	 * returns the array-data/exception message/excel-sheet path

	*/

	public function getGstReturnExcelPath($saleTaxData,$purchaseTaxResult,$stockResult,$incomeExpenseResult)

	{

		//get constant-data

		$constantClass = new ConstantClass();

		$constantArray = $constantClass->constantVariable();



		//get exception message

		$exception = new ExceptionMessage();

		$exceptionArray = $exception->messageArrays();

		$saleTaxDecodedData = array();

		$purchaseTaxDecodedData = array();

		$stockData = array();

		if(strcmp($saleTaxData,$exceptionArray['204'])!=0)

		{

			$saleTaxDecodedData = json_decode($saleTaxData);

		}

		if(strcmp($purchaseTaxResult,$exceptionArray['204'])!=0)

		{

			$purchaseTaxDecodedData = json_decode($purchaseTaxResult);

		}

		if(strcmp($stockResult,$exceptionArray['204'])!=0)

		{

			$stockData = json_decode($stockResult);

		}

		$incomeExpenseData = json_decode($incomeExpenseResult);

		// generate excel

		$objPHPExcel = new \PHPExcel();

		//first sheet (Sales Invoice)

		$objPHPExcel->setActiveSheetIndex(0);

		// Set properties comment

		$objPHPExcel->getProperties()->setCreator("ThinkPHP")

						->setLastModifiedBy("Daniel Schlichtholz")

						->setTitle("Office 2007 XLSX Test Document")

						->setSubject("Office 2007 XLSX Test Document")

						->setDescription("Test doc for Office 2007 XLSX, generated by PHPExcel.")

						->setKeywords("office 2007 openxml php")

						->setCategory("Test result file");

		$objPHPExcel->getActiveSheet(0)->setTitle('Sales Invoice');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,3, 'Sr.');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,3, 'Invoice Number');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,3, 'Invoice Date');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,3, 'Buyer\'s Name');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,3, 'Buyer\'s Gst No');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,3, 'Place of supplier(State)');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,3, 'Particulars');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,3, 'HSN Code');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,3, 'Taxable Value');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(10,3, 'GST Rate');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(11,3, 'CGST Amount');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(12,3, 'SGST Amount');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(13,3, 'IGST Amount');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(14,3, 'CESS Amount');

		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(15,3, 'Invoice Value(Total)');

		$dataCount = count($saleTaxDecodedData);

		$loopCount=0;

		for($data=0;$data<$dataCount;$data++)

		{

			$productCount = count($saleTaxDecodedData[$data]->product);

			$totalCgst=0;

			$totalSgst=0;

			$totalIgst=0;

			//calculating total-cgst & total-sgst

			for($productArray=0;$productArray<$productCount;$productArray++)

			{

				$totalCgst = $saleTaxDecodedData[$data]->product[$productArray]->product->vat;

				$totalSgst = $saleTaxDecodedData[$data]->product[$productArray]->product->additionalTax;

				$totalIgst = $saleTaxDecodedData[$data]->product[$productArray]->product->igst;

				

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$loopCount+4+$productArray,$data+1);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->invoiceNumber);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->entryDate);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->client->clientName);

				if(array_key_exists('ledger',$saleTaxDecodedData[$data]))

				{

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->ledger->cgst);

				}

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->client->state->stateName);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(7,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->product[$productArray]->product->productName);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(8,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->product[$productArray]->product->hsn);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(9,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->tax);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(10,$loopCount+4+$productArray,'GST Rate');

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(11,$loopCount+4+$productArray,$totalCgst);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(12,$loopCount+4+$productArray,$totalSgst);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(13,$loopCount+4+$productArray,$totalIgst);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(14,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->company->cess);

				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(15,$loopCount+4+$productArray,$saleTaxDecodedData[$data]->grandTotal);

			}

			$loopCount = $productCount+$data;

		}

		$styleArray = array(

			'borders' => array(

			  'allborders' => array(

				  'style' => PHPExcel_Style_Border::BORDER_THIN

			  )

			)

		);

		$borderCount = "P".($loopCount+4-1);

		$objPHPExcel->getActiveSheet()->getStyle("B3:".$borderCount)->applyFromArray($styleArray);

		

		//create 2nd sheet (purchase-invoice)

		$objPHPExcel->createSheet(1);

		$objPHPExcel->setActiveSheetIndex(1);

		$objPHPExcel->getActiveSheet(1)->setTitle('Purchase Invoice');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(1,3, 'Sr.');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(2,3, 'Invoice Number');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(3,3, 'Invoice Date');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(4,3, 'Buyer\'s Name');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(5,3, 'Buyer\'s Gst No');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6,3, 'Place of supplier(State)');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7,3, 'Particulars');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(8,3, 'HSN Code');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(9,3, 'Taxable Value');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(10,3, 'GST Rate');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(11,3, 'CGST Amount');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(12,3, 'SGST Amount');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(13,3, 'IGST Amount');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(14,3, 'CESS Amount');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(15,3, 'Invoice Value(Total)');

		//heading-end



		$loopCount=0;

		$purchaseDataCount = count($purchaseTaxDecodedData);

		for($data=0;$data<$purchaseDataCount;$data++)

		{

			$productCount = count($purchaseTaxDecodedData[$data]->product);

			//entry-date conversion

			$splitedDate = explode("-",trim($purchaseTaxDecodedData[$data]->entryDate));

			$entryDate[$data] = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];

			// calculating total-cgst & total-sgst

			for($productArray=0;$productArray<$productCount;$productArray++)

			{

				$totalCgst = $purchaseTaxDecodedData[$data]->product[$productArray]->product->vat;

				$totalSgst = $purchaseTaxDecodedData[$data]->product[$productArray]->product->additionalTax;

				$totalIgst = $purchaseTaxDecodedData[$data]->product[$productArray]->product->igst;

				

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(1,$loopCount+4+$productArray,$data+1);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(2,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->billNumber);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(3,$loopCount+4+$productArray,$entryDate[$data]);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(4,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->ledger->ledgerName);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(5,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->ledger->cgst);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->ledger->state->stateName);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->product[$productArray]->product->productName);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(8,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->product[$productArray]->product->hsn);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(9,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->tax);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(10,$loopCount+4+$productArray,'GST Rate');

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(11,$loopCount+4+$productArray,$totalCgst);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(12,$loopCount+4+$productArray,$totalSgst);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(13,$loopCount+4+$productArray,$totalIgst);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(14,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->company->cess);

				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(15,$loopCount+4+$productArray,$purchaseTaxDecodedData[$data]->grandTotal);

			}

			$loopCount = $productCount+$data;

		}

		$borderCount = "P".($loopCount+4-1);

		$objPHPExcel->getActiveSheet(1)->getStyle("B3:".$borderCount)->applyFromArray($styleArray);

		

		//create 3rd sheet (stock-detail)

		$objPHPExcel->createSheet(2);

		$objPHPExcel->setActiveSheetIndex(2);

		$objPHPExcel->getActiveSheet(2)->setTitle('Stock-Detail');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(1,3, 'Sr.');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(2,3, 'Particulars');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(3,3, 'HSN Code');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4,3, 'Opening Balance');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(6,3, 'Purchase');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(8,3, 'Total');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(10,3, 'Sales');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(12,3, 'Closing Stock');

		

		$objPHPExcel->setActiveSheetIndex(2)->mergeCells('E3:F3');

		$objPHPExcel->setActiveSheetIndex(2)->mergeCells('G3:H3');

		$objPHPExcel->setActiveSheetIndex(2)->mergeCells('I3:J3');

		$objPHPExcel->setActiveSheetIndex(2)->mergeCells('K3:L3');



		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4,4, 'Qty');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(5,4, 'Amount');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(6,4, 'Qty');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(7,4, 'Amount');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(8,4, 'Qty');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(9,4, 'Amount');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(10,4, 'Qty');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(11,4, 'Amount');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(12,4, 'Qty');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(13,4, 'Amount');

		//heading-end

		$indexingData = 0;

		$stockCount = count($stockData);	

		for($stockArray=0;$stockArray<$stockCount;$stockArray++)

		{

			$indexingData = 4+$stockArray+1;

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(1,$indexingData,$stockArray+1);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(2,$indexingData,$stockData[$stockArray]->productName);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(3,$indexingData,$stockData[$stockArray]->hsn);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4,$indexingData,$stockData[$stockArray]->openingQty);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(5,$indexingData,$stockData[$stockArray]->openingPrice);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(6,$indexingData,$stockData[$stockArray]->purchaseQty);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(7,$indexingData,$stockData[$stockArray]->purchasePrice);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(8,$indexingData,($stockData[$stockArray]->openingQty+$stockData[$stockArray]->purchaseQty));

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(9,$indexingData,($stockData[$stockArray]->openingPrice+$stockData[$stockArray]->purchasePrice));

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(10,$indexingData,$stockData[$stockArray]->saleQty);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(11,$indexingData,$stockData[$stockArray]->salePrice);

			$closingQty = ($stockData[$stockArray]->openingQty+$stockData[$stockArray]->purchaseQty)-$stockData[$stockArray]->saleQty;

			$closingPrice = ($stockData[$stockArray]->openingPrice+$stockData[$stockArray]->purchasePrice)- $stockData[$stockArray]->salePrice; 

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(12,$indexingData,$closingQty);

			$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(13,$indexingData,$closingPrice);

		}

		$borderCount = "N".($stockCount+4);

		$objPHPExcel->getActiveSheet(2)->getStyle("B3:".$borderCount)->applyFromArray($styleArray);

		//create 4th sheet (Income-Expense detail)

		$objPHPExcel->createSheet(3);

		$objPHPExcel->setActiveSheetIndex(3);

		$objPHPExcel->getActiveSheet(3)->setTitle('Income Expense Details');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(5,6, 'Trading Account');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(5,8, '	Profit & Loss Account');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(5,10, 'Balance Sheet');

		

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(7,6, $incomeExpenseData->tradingAmount);

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(7,8, $incomeExpenseData->profitLossAmount);

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(7,10, $incomeExpenseData->balancesheetAmount);



		$objPHPExcel->setActiveSheetIndex(0);

		// make unique name

		$dateTime = date("d-m-Y h-i-s");

		$convertedDateTime = str_replace(" ","-",$dateTime);

		$splitDateTime = explode("-",$convertedDateTime);

		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];

		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999)."_GSTReturn.xls"; //xslx

		$path = $constantArray['taxReturnUrl'];

		$documentPathName = $path.$documentName;

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		$objWriter->save($documentPathName);

		

		$pathArray = array();

		$pathArray['documentPath'] = $documentPathName;

		return $pathArray;

	}



	/**

	 * convert data

	 * returns the converted arraydata

	*/

	public function getGstr2Data($taxationData)

	{

		$decodedTaxationData = json_decode($taxationData);

		$decodedB2bData = $decodedTaxationData->b2b;

		$type = "";

		$resultData = array();

		if(count($decodedB2bData)>0)

		{

			$type = "b2b";

			$resultData[$type] = $this->getTaxationData($decodedB2bData,$type);

		}

		$decodedImpsData = $decodedTaxationData->imps;

		if(count($decodedImpsData)>0)

		{

			$type = "imps";

			$resultData[$type] = $this->getTaxationData($decodedImpsData,$type);

		}

		return json_encode($resultData);

	}

	/**

	 * convert data

	 * returns the converted arraydata

	*/

	public function getGstr3Data($taxationData)

	{

		$decodedTaxationData = json_decode($taxationData);

		$decodedr1InvoiceData = $decodedTaxationData->gstr1Invoice;

		$type = "";

		$resultData = array();

		if(count($decodedr1InvoiceData)>0)

		{

			$type = "gstr1Invoice";

			$resultData[$type] = $this->getTaxationData($decodedr1InvoiceData,$type);

		}
		return json_encode($resultData);
	}



	/**

	 * convert data

	 * returns the converted arraydata

	*/

	public function getTaxationData($taxationData,$type)

	{

		$data = array();

		$decodedCountData = count($taxationData);

		$companyService = new CompanyService();

		$clientService = new ClientService();

		for($dataArray=0;$dataArray<$decodedCountData;$dataArray++)

		{

			$clientData[$dataArray]  = $clientService->getClientData($taxationData[$dataArray]->client_id);

			$decodedClientData[$dataArray] = json_decode($clientData[$dataArray]);

			// convert amount(round) into their company's selected decimal points

			$companyData[$dataArray] = $companyService->getCompanyData($taxationData[$dataArray]->company_id);

			$companyDecodedData[$dataArray] = json_decode($companyData[$dataArray]);

			

			$taxationData[$dataArray]->total = number_format($taxationData[$dataArray]->total,$companyDecodedData[$dataArray]->noOfDecimalPoints);

			$taxationData[$dataArray]->total_discount = number_format($taxationData[$dataArray]->total_discount,$companyDecodedData[$dataArray]->noOfDecimalPoints);

			$taxationData[$dataArray]->extra_charge = number_format($taxationData[$dataArray]->extra_charge,$companyDecodedData[$dataArray]->noOfDecimalPoints);

			$taxationData[$dataArray]->tax = number_format($taxationData[$dataArray]->tax,$companyDecodedData[$dataArray]->noOfDecimalPoints);

			$taxationData[$dataArray]->grand_total = number_format($taxationData[$dataArray]->grand_total,$companyDecodedData[$dataArray]->noOfDecimalPoints,'.','');

			$taxationData[$dataArray]->advance = number_format($taxationData[$dataArray]->advance,$companyDecodedData[$dataArray]->noOfDecimalPoints);

			$taxationData[$dataArray]->balance = number_format($taxationData[$dataArray]->balance,$companyDecodedData[$dataArray]->noOfDecimalPoints);

			$taxationData[$dataArray]->refund = number_format($taxationData[$dataArray]->refund,$companyDecodedData[$dataArray]->noOfDecimalPoints);

			//date format conversion

			$taxationData[$dataArray]->entry_date = 

						strcmp($taxationData[$dataArray]->entry_date,'0000-00-00 00:00:00')==0

						? "00-00-0000"

						: Carbon\Carbon::createFromFormat('Y-m-d', $taxationData[$dataArray]->entry_date)->format('d-m-Y');



			$taxationData[$dataArray]->created_at = 

						strcmp($taxationData[$dataArray]->created_at,'0000-00-00 00:00:00')==0

						? "00-00-0000"

						: Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $taxationData[$dataArray]->created_at)->format('d-m-Y');

					

			$taxationData[$dataArray]->updated_at = 

						strcmp($taxationData[$dataArray]->updated_at,'0000-00-00 00:00:00')==0

						? "00-00-0000"

						: Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $taxationData[$dataArray]->updated_at)->format('d-m-Y');



			$data[$dataArray]= array(

				'saleId'=>$taxationData[$dataArray]->sale_id,

				'productArray'=>$taxationData[$dataArray]->product_array,

				'paymentMode'=>$taxationData[$dataArray]->payment_mode,

				'bankName'=>$taxationData[$dataArray]->bank_name,

				'invoiceNumber'=>$taxationData[$dataArray]->invoice_number,

				'jobCardNumber'=>$taxationData[$dataArray]->job_card_number,

				'checkNumber'=>$taxationData[$dataArray]->check_number,

				'total'=>$taxationData[$dataArray]->total,

				'totalDiscountType'=>$taxationData[$dataArray]->total_discounttype,

				'totalDiscount'=>$taxationData[$dataArray]->total_discount,

				'extraCharge'=>$taxationData[$dataArray]->extra_charge,

				'tax'=>$taxationData[$dataArray]->tax,

				'grandTotal'=>$taxationData[$dataArray]->grand_total,

				'advance'=>$taxationData[$dataArray]->advance,

				'balance'=>$taxationData[$dataArray]->balance,

				'poNumber'=>$taxationData[$dataArray]->po_number,

				'remark'=>$taxationData[$dataArray]->remark,

				'salesType'=>$taxationData[$dataArray]->sales_type,

				'refund'=>$taxationData[$dataArray]->refund,

				'jfId'=>$taxationData[$dataArray]->jf_id,

				'client'=>$decodedClientData[$dataArray],

				'company'=>$companyDecodedData[$dataArray],

				'createdAt'=>$taxationData[$dataArray]->created_at,

				'updatedAt'=>$taxationData[$dataArray]->updated_at,

				'entryDate'=>$taxationData[$dataArray]->entry_date

			);

		}

		return $data;

	}



	/**
	 * generate excel-sheet
	 * returns the document-path
	*/
	public function getGstR2ExcelPath($resultTaxationData)

	{

		//get constant-data

		$constantClass = new ConstantClass();

		$constantArray = $constantClass->constantVariable();

		// generate excel

		$objPHPExcel = new \PHPExcel();

		// Set properties comment

		$objPHPExcel->getProperties()->setCreator("ThinkPHP")

						->setLastModifiedBy("Daniel Schlichtholz")

						->setTitle("Office 2007 XLSX Test Document")

						->setSubject("Office 2007 XLSX Test Document")

						->setDescription("Test doc for Office 2007 XLSX, generated by PHPExcel.")

						->setKeywords("office 2007 openxml php")

						->setCategory("Test result file");

		$objPHPExcel = $this->getHelpExecelData($objPHPExcel,$constantArray);

		$objPHPExcel = $this->getB2bExecelData($resultTaxationData,$objPHPExcel);

		$objPHPExcel = $this->getB2burExecelData($resultTaxationData,$objPHPExcel);

		$objPHPExcel = $this->getImpsExecelData($resultTaxationData,$objPHPExcel);

		// $objPHPExcel = $this->getCdnrExecelData($resultTaxationData,$objPHPExcel);

		// $objPHPExcel = $this->getCdnurExecelData($resultTaxationData,$objPHPExcel);

		

		$objPHPExcel->setActiveSheetIndex(0);

		

		// make unique name

		$dateTime = date("d-m-Y h-i-s");

		$convertedDateTime = str_replace(" ","-",$dateTime);

		$splitDateTime = explode("-",$convertedDateTime);

		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];

		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999)."_GSTR2.xls"; //xslx

		

		$path = $constantArray['taxReturnUrl'];

		$documentPathName = $path.$documentName;

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		$objWriter->save($documentPathName);

		

		$pathArray = array();

		$pathArray['documentPath'] = $documentPathName;

		return $pathArray;

	}
	/**

	 * generate excel-sheet

	 * returns the phpexcel-object

	*/

	public function getHelpExecelData($objPHPExcel,$constantArray)

	{

		//create 1st sheet (B2b)

		$objPHPExcel->createSheet(0);

		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet(0)->setTitle('Help Instructions');



		$path = $constantArray['taxHtmlUrl'];

		$inputFileType = 'HTML';

		$inputFileName = $path.'sample.html';

		

		$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);

		$objPHPExcel = $objPHPExcelReader->load($inputFileName);

		

		// $objPHPExcel->setActiveSheetIndex(0)->setShowGridlines(false);

		return $objPHPExcel;

	}



	/**

	 * generate excel-sheet

	 * returns the phpexcel-object

	*/

	public function getB2bExecelData($resultTaxationData,$objPHPExcel)

	{
		//create 1st sheet (B2b)

		$objPHPExcel->createSheet(1);

		$objPHPExcel->setActiveSheetIndex(1);

		$objPHPExcel->getActiveSheet(1)->setTitle('b2b');

		//heading-start

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(0,1, 'Summary Of Supplies From Registered Suppliers B2B(3)');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(0,2, 'No. of Suppliers');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(1,2, 'No. of Invoices');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(3,2, 'Total Invoice Value');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(8,2, 'Total Taxable Value');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(9,2, 'Total Integrated Tax Paid');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(10,2, 'Total Central Tax Paid');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(11,2, 'Total TState/UT Tax Paid');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(12,2, 'Total Cess');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(14,2, 'Total Availed ITC Integrated Tax');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(15,2, 'Total Availed ITC Central Tax');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(16,2, 'Total Availed ITC State/UT Tax');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(17,2, 'Total Availed ITC Cess');

		

		//table heading

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(0,4, 'GSTIN of Supplier');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(1,4, 'Invoice Number');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(2,4, 'Invoice date');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(3,4, 'Invoice Value');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(4,4, 'Place Of Supply');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(5,4, 'Reverse Charge');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6,4, 'Invoice Type');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7,4, 'Rate');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(8,4, 'Taxable Value');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(9,4, 'Integrated Tax Paid');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(10,4, 'Central Tax Paid');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(11,4, 'State/UT Tax Paid');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(12,4, 'Cess Paid');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(13,4, 'Eligibility For ITC');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(14,4, 'Availed ITC Integrated Tax');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(15,4, 'Availed ITC Central Tax');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(16,4, 'Availed ITC State/UT Tax');

		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(17,4, 'Availed ITC Cess');

		$decodedTaxationData = json_decode($resultTaxationData);

		if(count($decodedTaxationData->b2b)>0)

		{
			$b2bCount = count($decodedTaxationData->b2b);
			$totalInvoiceValue=0;
			$totalTaxableValue=0;
			$totalCess=0;
			$totalIgstTax=0;
			$totalSgstTax=0;
			$totalCgstTax=0;
			for($arrayData=0;$arrayData<$b2bCount;$arrayData++)
			{
				$b2bData = $decodedTaxationData->b2b[$arrayData];
				$totalInvoiceValue = $totalInvoiceValue+$b2bData->grandTotal;
				$rowIndex = $arrayData+5;
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(0,$rowIndex,$b2bData->company->cgst);
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(1,$rowIndex,$b2bData->invoiceNumber);
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(2,$rowIndex,$b2bData->entryDate);
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(3,$rowIndex,$b2bData->grandTotal);
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(4,$rowIndex,$b2bData->company->state->stateName);
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(5,$rowIndex,'N');
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6,$rowIndex,'Regular');
				$decodedProductArray = json_decode($b2bData->productArray);
				$innerArrayCount = count($decodedProductArray->inventory);
				$totalCgst=0;
				$totalCgstPer=0;
				$totalSgstPer=0;
				$totalIgstPer=0;
				$totalSgst=0;
				$totalIgst=0;
				for($innerArray=0;$innerArray<$innerArrayCount;$innerArray++)
				{
					if(isset($decodedProductArray->inventory[$innerArray]->cgstAmount))
					{
						$totalCgst = $totalCgst+$decodedProductArray->inventory[$innerArray]->cgstAmount;
						$totalCgstPer = $totalCgstPer+$decodedProductArray->inventory[$innerArray]->cgstPercentage;
					}
					if(isset($decodedProductArray->inventory[$innerArray]->sgstAmount))
					{
						$totalSgst = $totalSgst+$decodedProductArray->inventory[$innerArray]->sgstAmount;
						$totalSgstPer = $totalSgstPer+$decodedProductArray->inventory[$innerArray]->sgstPercentage;
					}
					if(isset($decodedProductArray->inventory[$innerArray]->igstAmount))
					{
						$totalIgst = $totalIgst+$decodedProductArray->inventory[$innerArray]->igstAmount;
						$totalIgstPer = $totalIgstPer+$decodedProductArray->inventory[$innerArray]->igstPercentage;
					}
				}
				$taxableValue=0;
				$rate=0;
				$grandTotal=0;
				$totalTax=0;
				$rate = $totalCgstPer+$totalSgstPer+$totalIgstPer;
				$grandTotal = floatval(str_replace(",","",$b2bData->grandTotal));
				$totalTax = floatval(str_replace(",","",($totalCgst+$totalSgst+$totalIgst)));
				$taxableValue = $grandTotal-($totalTax);
				$totalTaxableValue = $totalTaxableValue +$taxableValue;
				$taxableValue = number_format($taxableValue,$b2bData->company->noOfDecimalPoints,'.','');
				$totalIgstTax = $totalIgstTax+$totalIgst;
				$totalCgstTax = $totalCgstTax+$totalCgst;
				$totalSgstTax = $totalSgstTax+$totalSgst;
				$cess = ($b2bData->company->cess/100)*$grandTotal;
				$totalCess = $totalCess+$cess;
				// $taxableValue = $b2bData->grandTotal-($totalCgst+$totalSgst+$totalIgst);
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7,$rowIndex,$rate);	
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(8,$rowIndex,$taxableValue);	
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(9,$rowIndex,$totalIgst);	
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(10,$rowIndex,$totalCgst);	
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(11,$rowIndex,$totalSgst);	
				$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(12,$rowIndex,$cess);
			}
		}
		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(1,3,$b2bCount);
		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(3,3,$totalInvoiceValue);
		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(8,3,$totalTaxableValue);
		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(9,3,$totalIgstTax);
		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(10,3,$totalCgstTax);
		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(11,3,$totalSgstTax);
		$objPHPExcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow(12,3,$totalCess);
		// style for header
		$headerStyleArray = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 10,
			'name'  => 'Verdana'
		),
		'fill'=>array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2F75B5')
		));
		// set header style
		$objPHPExcel->getActiveSheet(1)->getStyle('A1')->applyFromArray($headerStyleArray);
		$objPHPExcel->getActiveSheet(1)->getStyle('A2:R2')->applyFromArray($headerStyleArray);
		return $objPHPExcel;
	}
	/**
	 * generate excel-sheet
	 * returns the phpexcel-object
	*/
	public function getB2burExecelData($resultTaxationData,$objPHPExcel)

	{

		//create 1st sheet (B2b)

		$objPHPExcel->createSheet(2);

		$objPHPExcel->setActiveSheetIndex(2);

		$objPHPExcel->getActiveSheet(2)->setTitle('b2bur');

		//heading-start

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(0,1, 'Summary Of Supplies From Unregistered Suppliers B2BUR(4B)');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(0,2, '');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(1,2, 'No. of Invoices (Of Reg Recipient)');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(3,2, 'Total Invoice Value');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(7,2, 'Total Taxable Value');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(8,2, 'Total Integrated Tax Paid');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(9,2, 'Total Central Tax Paid');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(10,2, 'Total TState/UT Tax Paid');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(11,2, 'Total Cess Paid');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(13,2, 'Total Availed ITC Integrated Tax');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(14,2, 'Total Availed ITC Central Tax');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(15,2, 'Total Availed ITC State/UT Tax');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(16,2, 'Total Availed ITC Cess');

		

		//table heading

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(0,4, 'Supplier Name');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(1,4, 'Invoice Number');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(2,4, 'Invoice date');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(3,4, 'Invoice Value');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4,4, 'Place Of Supply');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(5,4, 'Supply Type');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(6,4, 'Rate');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(7,4, 'Taxable Value');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(8,4, 'Integrated Tax Paid');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(9,4, 'Central Tax Paid');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(10,4, 'State/UT Tax Paid');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(11,4, 'Cess Paid');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(12,4, 'Eligibility For ITC');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(13,4, 'Availed ITC Integrated Tax');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(14,4, 'Availed ITC Central Tax');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(15,4, 'Availed ITC State/UT Tax');

		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(16,4, 'Availed ITC Cess');

		$decodedTaxationData = json_decode($resultTaxationData);
		if(count($decodedTaxationData->b2b)>0)
		{
			$b2burCount = count($decodedTaxationData->b2b);
			$totalInvoiceValue=0;
			$totalTaxableValue=0;
			$totalIgstTax=0;
			$totalSgstTax=0;
			$totalCgstTax=0;
			$totalCess=0;
			for($arrayData=0;$arrayData<$b2burCount;$arrayData++)
			{
				$b2bData = $decodedTaxationData->b2b[$arrayData];
				$totalInvoiceValue  =$totalInvoiceValue+$b2bData->grandTotal;
				$rowIndex = $arrayData+5;
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(0,$rowIndex,$b2bData->client->clientName);
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(1,$rowIndex,$b2bData->invoiceNumber);
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(2,$rowIndex,$b2bData->entryDate);
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(3,$rowIndex,$b2bData->grandTotal);
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4,$rowIndex,$b2bData->company->state->stateName);
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(5,$rowIndex,'Inter State');
				$decodedProductArray = json_decode($b2bData->productArray);
				$innerArrayCount = count($decodedProductArray->inventory);
				$totalCgstPer=0;
				$totalSgstPer=0;
				$totalIgstPer=0;
				$totalCgst=0;
				$totalSgst=0;
				$totalIgst=0;
				for($innerArray=0;$innerArray<$innerArrayCount;$innerArray++)
				{
					if(isset($decodedProductArray->inventory[$innerArray]->cgstAmount))
					{
						$totalCgst = $totalCgst+$decodedProductArray->inventory[$innerArray]->cgstAmount;
						$totalCgstPer = $totalCgstPer+$decodedProductArray->inventory[$innerArray]->cgstPercentage;
					}
					if(isset($decodedProductArray->inventory[$innerArray]->sgstAmount))
					{
						$totalSgst = $totalSgst+$decodedProductArray->inventory[$innerArray]->sgstAmount;
						$totalSgstPer = $totalSgstPer+$decodedProductArray->inventory[$innerArray]->sgstPercentage;
					}
					if(isset($decodedProductArray->inventory[$innerArray]->igstAmount))
					{
						$totalIgst = $totalIgst+$decodedProductArray->inventory[$innerArray]->igstAmount;
						$totalIgstPer = $totalIgstPer+$decodedProductArray->inventory[$innerArray]->igstPercentage;
					}	
				}
				
				$taxableValue=0;
				$rate=0;
				$rate = $totalCgstPer+$totalSgstPer+$totalIgstPer;
				$grandTotal=0;
				$totalTax=0;
				$grandTotal = floatval(str_replace(",","",$b2bData->grandTotal));
				$totalTax = floatval(str_replace(",","",($totalCgst+$totalSgst+$totalIgst)));
				$taxableValue = $grandTotal-($totalTax);
				$totalTaxableValue = $totalTaxableValue+$taxableValue;
				$taxableValue = number_format($taxableValue,$b2bData->company->noOfDecimalPoints,'.','');

				$cess = ($b2bData->company->cess/100)*$grandTotal;
				$totalCess = $totalCess+$cess;
				$totalIgstTax = $totalIgstTax+$totalIgst;
				$totalSgstTax = $totalSgstTax+$totalSgst;
				$totalCgstTax = $totalCgstTax+$totalCgst;
				// $taxableValue = $b2bData->grandTotal-($totalCgst+$totalSgst+$totalIgst);
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(6,$rowIndex,$rate);	
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(7,$rowIndex,$taxableValue);	
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(8,$rowIndex,$totalIgst);	
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(9,$rowIndex,$totalCgst);	
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(10,$rowIndex,$totalSgst);	
				$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(11,$rowIndex,$cess);
			}
		}
		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(1,3,$b2burCount);
		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(3,3,$totalInvoiceValue);
		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(7,3,$totalTaxableValue);
		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(8,3,$totalIgstTax);
		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(9,3,$totalCgstTax);
		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(10,3,$totalSgstTax);
		$objPHPExcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow(11,3,$totalCess);
		// style for header
		$headerStyleArray = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 10,
			'name'  => 'Verdana'
		),
		'fill'=>array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2F75B5')
		));
		// set header style
		$objPHPExcel->getActiveSheet(2)->getStyle('A1')->applyFromArray($headerStyleArray);
		$objPHPExcel->getActiveSheet(2)->getStyle('A2:Q2')->applyFromArray($headerStyleArray);
		return $objPHPExcel;
	}
	/**
	 * generate excel-sheet
	 * returns the phpexcel-object
	*/
	public function getImpsExecelData($resultTaxationData,$objPHPExcel)
	{

		//create 1st sheet (B2b)

		$objPHPExcel->createSheet(3);

		$objPHPExcel->setActiveSheetIndex(3);

		$objPHPExcel->getActiveSheet(3)->setTitle('imps');

		//heading-start

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(0,1, 'Summary For IMPS (4C)');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(0,2, 'No. of Invoices (Of Reg Recipient)');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(2,2, 'Total Invoice Value');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(5,2, 'Total Taxable Value');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(6,2, 'Total Integrated Tax Paid');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(7,2, 'Total Cess Paid');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(9,2, 'Total Availed ITC Integrated Tax');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(10,2, 'Total Availed ITC Cess');

		

		//table heading

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(0,4, 'Invoice Number of Reg Recipient');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(1,4, 'Invoice date');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(2,4, 'Invoice Value');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(3,4, 'Place Of Supply');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(4,4, 'Rate');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(5,4, 'Taxable Value');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(6,4, 'Integrated Tax Paid');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(7,4, 'Cess Paid');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(8,4, 'Eligibility For ITC');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(9,4, 'Availed ITC Integrated Tax');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(10,4, 'Availed ITC Cess');

		$decodedTaxationData = json_decode($resultTaxationData);
		if(count($decodedTaxationData->b2b)>0)
		{
			$impsCount = count($decodedTaxationData->b2b);
			$totalInvoiceValue=0;
			$totalTaxableValue=0;
			$totalIgstTax=0;
			$totalCess=0;				
			for($arrayData=0;$arrayData<$impsCount;$arrayData++)
			{
				$b2bData = $decodedTaxationData->b2b[$arrayData];
				$totalInvoiceValue = $totalTaxableValue+$b2bData->grandTotal;
				$rowIndex = $arrayData+5;
				$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(0,$rowIndex,$b2bData->invoiceNumber);
				$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(1,$rowIndex,$b2bData->entryDate);
				$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(2,$rowIndex,$b2bData->grandTotal);
				$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(3,$rowIndex,$b2bData->company->state->stateName);
				$decodedProductArray = json_decode($b2bData->productArray);
				$innerArrayCount = count($decodedProductArray->inventory);
				$totalCgstPer=0;
				$totalSgstPer=0;
				$totalIgstPer=0;
				$totalCgst=0;
				$totalSgst=0;
				$totalIgst=0;
				for($innerArray=0;$innerArray<$innerArrayCount;$innerArray++)
				{
					if(isset($decodedProductArray->inventory[$innerArray]->cgstAmount))
					{
						$totalCgst = $totalCgst+$decodedProductArray->inventory[$innerArray]->cgstAmount;
						$totalCgstPer = $totalCgstPer+$decodedProductArray->inventory[$innerArray]->cgstPercentage;
					}
					if(isset($decodedProductArray->inventory[$innerArray]->sgstAmount))
					{
						$totalSgst = $totalSgst+$decodedProductArray->inventory[$innerArray]->sgstAmount;
						$totalSgstPer = $totalSgstPer+$decodedProductArray->inventory[$innerArray]->sgstPercentage;
					}
					if(isset($decodedProductArray->inventory[$innerArray]->igstAmount))
					{
						$totalIgst = $totalIgst+$decodedProductArray->inventory[$innerArray]->igstAmount;
						$totalIgstPer = $totalIgstPer+$decodedProductArray->inventory[$innerArray]->igstPercentage;
					}	
				}
				$taxableValue=0;
				$rate=0;
				$rate = $totalCgstPer+$totalSgstPer+$totalIgstPer;
				$grandTotal=0;
				$totalTax=0;
				$grandTotal = floatval(str_replace(",","",$b2bData->grandTotal));
				$totalTax = floatval(str_replace(",","",($totalCgst+$totalSgst+$totalIgst)));
				$taxableValue = $grandTotal-($totalTax);
				$totalTaxableValue = $totalTaxableValue +$taxableValue;
				$taxableValue = number_format($taxableValue,$b2bData->company->noOfDecimalPoints,'.','');
				$totalIgstTax = $totalIgstTax+$totalIgst;

				$cess = ($b2bData->company->cess/100)*$grandTotal;
				$totalCess = $totalCess+$cess;
				// $taxableValue = $b2bData->grandTotal-($totalCgst+$totalSgst+$totalIgst);
				$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(4,$rowIndex,$rate);	
				$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(5,$rowIndex,$taxableValue);	
				$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(6,$rowIndex,$totalIgst);	
				$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(7,$rowIndex,$cess);
			}
		}
		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(0,3,$impsCount);
		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(2,3,$totalInvoiceValue);
		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(5,3,$totalTaxableValue);
		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(6,3,$totalIgstTax);
		$objPHPExcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow(7,3,$totalCess);
		
		// style for header
		$headerStyleArray = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 10,
			'name'  => 'Verdana'
		),
		'fill'=>array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2F75B5')
		));
		// set header style
		$objPHPExcel->getActiveSheet(3)->getStyle('A1')->applyFromArray($headerStyleArray);
		$objPHPExcel->getActiveSheet(3)->getStyle('A2:K2')->applyFromArray($headerStyleArray);
		return $objPHPExcel;
	}



	/**
	 * generate excel-sheet
	 * returns the phpexcel-object
	*/
	public function getCdnrExecelData($resultTaxationData,$objPHPExcel)
	{
		//create 1st sheet (B2b)
		$objPHPExcel->createSheet(4);
		$objPHPExcel->setActiveSheetIndex(4);
		$objPHPExcel->getActiveSheet(4)->setTitle('cdnr');
		//heading-start
		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(0,1, 'Summary For CDNR(6C)');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(0,2, 'No. of Supplier');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(1,2, 'No. of Notes/Vouchers');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(3,2, 'No. of Invoices');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(9,2, 'Total Note/Voucher Value');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(11,2, 'Total Taxable Value');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(12,2, 'Total Integrated Tax Paid');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(13,2, 'Total Central Tax Paid');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(14,2, 'Total TState/UT Tax Paid');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(15,2, 'Total Cess');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(17,2, 'Total Availed ITC Integrated Tax');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(18,2, 'Total Availed ITC Central Tax');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(19,2, 'Total Availed ITC State/UT Tax');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(20,2, 'Total Availed ITC Cess');





		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(0,4, 'GSTIN of Supplier');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(1,4, 'Note/Refund Voucher Number');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(2,4, 'Note/Refund Voucher date');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(3,4, 'Invoice/Advance Payment Voucher Number');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(4,4, 'Invoice/Advance Payment Voucher date');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(5,4, 'Pre GST');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(6,4, 'Document Type');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(7,4, 'Reason For Issuing document');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(8,4, 'Supply Type');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(9,4, 'Note/Refund Voucher Value');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(10,4, 'Rate');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(11,4, 'Taxable Value');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(12,4, 'Integrated Tax Paid');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(13,4, 'Central Tax Paid');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(14,4, 'State/UT Tax Paid');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(15,4, 'Cess Paid');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(16,4, 'Eligibility For ITC');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(17,4, 'Availed ITC Integrated Tax');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(18,4, 'Availed ITC Central Tax');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(19,4, 'Availed ITC State/UT Tax');

		$objPHPExcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow(20,4, 'Availed ITC Cess');

	}

	/**
	 * generate excel-sheet
	 * returns the document-path
	*/
	public function getGstR3ExcelPath($resultTaxationData)
	{
		//get constant-data
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		// generate excel
		$objPHPExcel = new \PHPExcel();
		// Set properties comment
		$objPHPExcel->getProperties()->setCreator("ThinkPHP")

						->setLastModifiedBy("Daniel Schlichtholz")

						->setTitle("Office 2007 XLSX Test Document")

						->setSubject("Office 2007 XLSX Test Document")

						->setDescription("Test doc for Office 2007 XLSX, generated by PHPExcel.")

						->setKeywords("office 2007 openxml php")

						->setCategory("Test result file");
		$objPHPExcel = $this->getR1InvoiceExcelData($resultTaxationData,$objPHPExcel,true);
		$objPHPExcel->setActiveSheetIndex(0);
		// make unique name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999)."_GSTR3.xls"; //xslx
		$path = $constantArray['taxReturnUrl'];
		$documentPathName = $path.$documentName;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($documentPathName);
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	public function getR1InvoiceExcelData($resultTaxationData,$objPHPExcel,$gstr3 = false)
	{
		//create 1st sheet (r1 invoice)
		$objPHPExcel->createSheet(0);
		$objPHPExcel->setActiveSheetIndex(0);
		if ($gstr3)
		{
			$objPHPExcel->getActiveSheet(0)->setTitle('GSTR3 Invoice');
		}
		else{
			$objPHPExcel->getActiveSheet(0)->setTitle('GSTR1 Invoice');
		}
		
		//heading-start
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,1, 'Index');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,1, 'Invoice Date');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,1, 'Invoice Number');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,1, 'Customer Billing Name');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,1, 'Customer Billing GSTIN');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,1, 'State Place of Supply');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,1, 'Is the item a GOOD (G) or SERVICE (S)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,1, 'Item Description');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,1, 'HSN or SAC code');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,1, 'Item Quantity');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,1, 'Item Unit of Measurement');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,1, 'Item Rate');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,1, 'Total Item Discount Amount');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,1, 'Item Taxable Value');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,1, 'CGST Rate');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15,1, 'CGST Amount');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(16,1, 'SGST Rate');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(17,1, 'SGST Amount');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(18,1, 'IGST Rate');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(19,1, 'IGST Amount');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(20,1, 'CESS Rate');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(21,1, 'CESS Amount');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(22,1, 'Is this a Bill of Supply?');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(23,1, 'Is Reverse Charge Applicable?');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(24,1, 'Is this a Nil Rated/Exempt/NonGST item?');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(25,1, 'Original Invoice Date (In case of amendment)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(26,1, 'Original Invoice Number (In case of amendment)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(27,1, 'Original Customer Billing GSTIN (In case of amendment)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(28,1, 'GSTIN of Ecommerce Marketplace');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(29,1, 'Date of Linked Advance Receipt');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(30,1, 'Voucher Number of Linked Advance Receipt');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(31,1, 'Adjustment Amount of the Linked Advance Receipt');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(32,1, 'Type of Export');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(33,1, 'Shipping Port Code - Export');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(34,1, 'Shipping Bill Number - Export');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(35,1, 'Shipping Bill Date - Export');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(36,1, 'Has GST/IDT TDS been deducted');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(37,1, 'My GSTIN');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(38,1, 'Customer Billing Address');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(39,1, 'Customer Billing City');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(40,1, 'Customer Billing State');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(41,1, 'Is this document cancelled?');
		$decodedTaxationData = json_decode($resultTaxationData);
		if(count($decodedTaxationData->gstr1Invoice)>0)
		{
			$r1InvoiceCount = count($decodedTaxationData->gstr1Invoice);
			for($arrayData=0;$arrayData<$r1InvoiceCount;$arrayData++)
			{
				//get product data as per given product-id
				// $productService = new ProductService();
				// $productData = $productService->getProductData();
				// print_r($productData);
				$gstR1Invoice = $decodedTaxationData->gstr1Invoice[$arrayData];
				$rowIndex = $arrayData+2;

				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$rowIndex,$arrayData+1);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$rowIndex,$gstR1Invoice->entryDate);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$rowIndex,$gstR1Invoice->invoiceNumber);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$rowIndex,$gstR1Invoice->client->clientName);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$rowIndex,$gstR1Invoice->company->cgst);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$rowIndex,$gstR1Invoice->company->state->stateName);

				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$rowIndex,'Good');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$rowIndex,'product-description');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$rowIndex,'product-hsncode');
				$decodedProductArray = json_decode($gstR1Invoice->productArray);
				$innerArrayCount = count($decodedProductArray->inventory);
				$totalCgstPer=0;
				$totalSgstPer=0;
				$totalIgstPer=0;
				$totalCgst=0;
				$totalSgst=0;
				$totalIgst=0;
				$totalDiscount = 0;
				$totalAmount=0;
				$totalPrice=0;
				for($innerArray=0;$innerArray<$innerArrayCount;$innerArray++)
				{
					if(isset($decodedProductArray->inventory[$innerArray]->amount))
					{
						$totalAmount=$totalAmount+$decodedProductArray->inventory[$innerArray]->amount;
					}
					if(strcmp($decodedProductArray->inventory[$innerArray]->discountType,'flat')==0)
					{
						$totalDiscount=$totalDiscount+$decodedProductArray->inventory[$innerArray]->discount;
					}
					else
					{
						$totalPrice=$decodedProductArray->inventory[$innerArray]->price*$decodedProductArray->inventory[$innerArray]->qty;
						$totalDiscount= $totalDiscount+(($decodedProductArray->inventory[$innerArray]->discount/100)*($totalPrice));
					}
					if(isset($decodedProductArray->inventory[$innerArray]->cgstAmount))
					{
						$totalCgst = $totalCgst+$decodedProductArray->inventory[$innerArray]->cgstAmount;
						$totalCgstPer = $totalCgstPer+$decodedProductArray->inventory[$innerArray]->cgstPercentage;
					}
					if(isset($decodedProductArray->inventory[$innerArray]->sgstAmount))
					{
						$totalSgst = $totalSgst+$decodedProductArray->inventory[$innerArray]->sgstAmount;
						$totalSgstPer = $totalSgstPer+$decodedProductArray->inventory[$innerArray]->sgstPercentage;
					}
					if(isset($decodedProductArray->inventory[$innerArray]->igstAmount))
					{
						$totalIgst = $totalIgst+$decodedProductArray->inventory[$innerArray]->igstAmount;
						$totalIgstPer = $totalIgstPer+$decodedProductArray->inventory[$innerArray]->igstPercentage;
					}	
				}

				$totalAmount = $totalAmount+$gstR1Invoice->extraCharge;
				if(strcmp($gstR1Invoice->totalDiscountType,'flat')==0)
				{
					$totalDiscount=$totalDiscount+$gstR1Invoice->totalDiscount;
				}
				else
				{
					$totalDiscount= $totalDiscount+(($gstR1Invoice->totalDiscount/100)*($totalAmount));
				}
				$taxableValue=0;
				$rate=0;
				$rate = $totalCgstPer+$totalSgstPer+$totalIgstPer;
				$grandTotal=0;
				$totalTax=0;
				$grandTotal = floatval(str_replace(",","",$gstR1Invoice->grandTotal));
				$cessAmount = ($gstR1Invoice->company->cess/100)*$grandTotal;
				$totalTax = floatval(str_replace(",","",($totalCgst+$totalSgst+$totalIgst)));
				$taxableValue = $grandTotal-($totalTax);
				$taxableValue = number_format($taxableValue,$gstR1Invoice->company->noOfDecimalPoints,'.','');
				$taxableValue = $gstR1Invoice->grandTotal-($totalCgst+$totalSgst+$totalIgst);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$rowIndex,'item qty');	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$rowIndex,'uom');	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$rowIndex,$gstR1Invoice->grandTotal);	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$rowIndex,$totalDiscount);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$rowIndex,$taxableValue);	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$rowIndex,$totalCgstPer);	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15,$rowIndex,$totalCgst);	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(16,$rowIndex,$totalSgstPer);	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(17,$rowIndex,$totalSgst);	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(18,$rowIndex,$totalIgstPer);	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(19,$rowIndex,$totalIgst);	
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(20,$rowIndex,$gstR1Invoice->company->cess);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(21,$rowIndex,$cessAmount);
			}
		}

		// style for header
		$headerStyleArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 10,
				'name'  => 'Verdana'
			),
			'fill'=>array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FCE5CD')
		));

		// set header style
		$objPHPExcel->getActiveSheet(0)->getStyle('A1:AP1')->applyFromArray($headerStyleArray);
		return $objPHPExcel;
	}

	public function getGSTR3BData($status,$innerState,$purchaseTaxData,$headerData)
	{
		$temp_head = $headerData;
		unset($temp_head['operation']);

		$constantClass = new ConstantClass();

		$constantArray = $constantClass->constantVariable();

		//get exception message
		$exception = new ExceptionMessage();

		$exceptionArray = $exception->messageArrays();

		

		$decodedJson = json_decode($status,true);


		$companyService = new CompanyService();
		$noOfDecimalPoints = 2;

		$data = array();

		$totalAmount = 0;

		$totalAdditioanalTax = 0;

		$totalGrandTotal = 0;

		$overallTotalTax = 0;
		$overallTotalCgst = 0;
		$overallTotalSgst = 0;
		$overallTotalIgst = 0;

		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$totalTax = 0;
			// echo "<pre>";
			// print_r($decodedJson[$decodedData]);
			// exit;
			$totalCgst = 0;
			$totalSgst = 0;
			$totalIgst = 0;

			$calculateAdditionalTax=0;

			$calculateVat=0;

			$decodedProductArrayData = json_decode($decodedJson[$decodedData]['product_array']);

			$productDataArray = array();

			$inventoryCount = count($decodedProductArrayData->inventory);

			

			for($arrayData=0;$arrayData<$inventoryCount;$arrayData++)
			{
				if($decodedProductArrayData->inventory[$arrayData]->productId != '')
				{
					$productService = new EncodeTaxationData();

					$productData = $productService->getProductData($decodedProductArrayData->inventory[$arrayData]->productId);

					$productDecodedData = json_decode($productData);

					// $vat = ($productDecodedData->purchasePrice/100)*$productDecodedData->vat;
					$vat = $decodedProductArrayData->inventory[$arrayData]->cgstAmount;

					$calculateVat = $calculateVat+$vat;

					$totalCgst = $totalCgst + $vat;

					// $additionalTax = ($productDecodedData->purchasePrice/100)*$productDecodedData->additionalTax;
					$additionalTax = $decodedProductArrayData->inventory[$arrayData]->sgstAmount;

					$calculateAdditionalTax = $calculateAdditionalTax+$additionalTax;

					$totalSgst = $totalSgst + $additionalTax;

					/* IGST */
						$igst = $decodedProductArrayData->inventory[$arrayData]->igstAmount;

						$totalIgst = $totalIgst + $igst;
					/* IGST */

					$productDataArray[$arrayData] = $decodedProductArrayData->inventory[$arrayData];

					$productDataArray[$arrayData]->product = $productDecodedData;
				}
			}


			$tax[$decodedData] = $decodedJson[$decodedData]['tax'];

			// convert amount(round) into their company's selected decimal points
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			$companyData[$decodedData] = $companyService->getCompanyData($companyId[$decodedData]);

			$companyDecodedData[$decodedData] = json_decode($companyData[$decodedData]);
			$noOfDecimalPoints = $companyDecodedData[$decodedData]->noOfDecimalPoints;
			// $totalAmount  = $totalAmount+$total[$decodedData];

			$totalTax = $totalTax+$totalCgst+$totalSgst+$totalIgst;

			$totalAdditioanalTax = $totalAdditioanalTax+$calculateAdditionalTax;

			/* Overall tax calculation */
				$overallTotalCgst = $overallTotalCgst + $totalCgst;
				$overallTotalSgst = $overallTotalSgst + $totalSgst;
				$overallTotalIgst = $overallTotalIgst + $totalIgst;

				$overallTotalTax = $overallTotalTax + $totalTax;
			/* End */
			

		}


		$data["outward"] = array(
			array(
				"perticular" => "(a) Outward taxable supplies (other than zero rated, nil rated and exempted)",
				"totalTax" => number_format($overallTotalTax,$noOfDecimalPoints),
				"cgst" => number_format($overallTotalCgst,$noOfDecimalPoints),
				"sgst" => number_format($overallTotalSgst,$noOfDecimalPoints),
				"igst" => number_format($overallTotalIgst,$noOfDecimalPoints),
				"cess" => 0
			),
			array(
				"perticular" => "(b) outward supplies, (Nil rated, exempted, Zero Tax Sales)",
				"totalTax" => 0,
				"cgst" => 0,
				"sgst" => 0,
				"igst" => 0,
				"cess" => 0
			)
		);

		/* Inner-state calculation */
			$innerStateData = $innerState;
		/* End */

		$data["innerState"] = array(
			array(
				"perticular" => "(a) Inter-State Taxable Supplies made to Unregistered Persons",
				"placeOfSupply" => 0,
				"totalTax" => 0,
				"integratedTax" => 0
			),
			array(
				"perticular" => "(b) Nil Rated Supplies made to Unregistered Persons Zero Tax Sales",
				"placeOfSupply" => 0,
				"totalTax" => 0,
				"integratedTax" => 0
			)
		);


		/* inward calculation */
			$inwardData = json_decode($this->getPurchaseTaxEncodedAllData($purchaseTaxData,$temp_head),true);
			
			$overallInnerCgst = 0;
			$overallInnerSgst = 0;
			$overallInnerIgst = 0;
			$overallInnerTotalTax = 0;

			$inner_index = 0;
			foreach ($inwardData as $inw)
			{
				$overallInnerCgst = $overallInnerCgst + str_replace(",","",$inw["totalCgst"]);
				$overallInnerSgst = $overallInnerSgst + str_replace(",","",$inw["totalSgst"]);
				$overallInnerIgst = $overallInnerIgst + str_replace(",","",$inw["totalIgst"]);
				$overallInnerTotalTax = $overallInnerTotalTax + str_replace(",","",$inw["totalTax"]);

				$inner_index++;
			}
		/* End */

		$data["inward"] = array(
			array(
				"perticular" => "(a) Inward taxable supplies (other than zero rated, nil rated and exempted)",
				"totalTax" => number_format($overallInnerTotalTax,$noOfDecimalPoints),
				"cgst" => number_format($overallInnerCgst,$noOfDecimalPoints),
				"sgst" => number_format($overallInnerSgst,$noOfDecimalPoints),
				"igst" => number_format($overallInnerIgst,$noOfDecimalPoints),
				"cess" => 0
			),
			array(
				"perticular" => "(B) Inward Taxable supplies (liable to reverse charge) URD Purchase",
				"totalTax" => 0,
				"cgst" => 0,
				"sgst" => 0,
				"igst" => 0,
				"cess" => 0
			),
			array(
				"perticular" => "(c) Inward supplies, (Zero rated, Nil rated, exempted)",
				"totalTax" => 0,
				"cgst" => 0,
				"sgst" => 0,
				"igst" => 0,
				"cess" => 0
			)
		);



		if(array_key_exists('operation',$headerData))

		{

			if(strcmp($headerData['operation'][0],'excel')==0)

			{

				// generate excel

				$objPHPExcel = new \PHPExcel();

				// Set properties comment

				$objPHPExcel->getProperties()->setCreator("ThinkPHP")

								->setLastModifiedBy("Daniel Schlichtholz")

								->setTitle("Office 2007 XLSX Test Document")

								->setSubject("Office 2007 XLSX Test Document")

								->setDescription("Test doc for Office 2007 XLSX, generated by PHPExcel.")

								->setKeywords("office 2007 openxml php")

								->setCategory("Test result file");

				$headerStyleArray = array(

					'font'  => array(

						'bold'  => true,

						'color' => array('rgb' => '#00000'),

						'size'  => 10,

						'name'  => 'Verdana'

					));

				$titleStyleArray = array(

				'font'  => array(

					'bold'  => true,

					'color' => array('rgb' => 'Black'),

					'size'  => 15,

					'name'  => 'Verdana'

				));

					$styleArray = array(

						'borders' => array(

						  'allborders' => array(

							  'style' => PHPExcel_Style_Border::BORDER_THIN

						  )

						)

					);

				/* Sheet Generate and Data */
					$objPHPExcel->getActiveSheet(0)->setTitle('GSTR3B');

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,2, 'Detail of Outward Supplies -Sales : (With and Without GST No - Local State and With GST No - Out State)');

					$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:G2');
					$objPHPExcel->getActiveSheet(0)->getStyle("B2:G2")->applyFromArray($titleStyleArray);


					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,4, 'Number of Supplies');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,4, 'Total Taxable Value');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,4, 'Integrated Tax');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,4, 'Central Tax');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,4, 'State/UT Tax');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,4, 'Cess');

					$out_excel_ind = 5;
					foreach ($data["outward"] as $otw) 
					{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$out_excel_ind, $otw["perticular"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$out_excel_ind, $otw["totalTax"]);
 
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$out_excel_ind, $otw["igst"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$out_excel_ind, $otw["cgst"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$out_excel_ind, $otw["sgst"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$out_excel_ind, $otw["cess"]);

						$out_excel_ind++;
					}

					// $borderCount = "P".($loopCount+4-1);0
					$objPHPExcel->getActiveSheet(0)->getStyle("B4:G4")->applyFromArray($headerStyleArray);

					


					//create 2nd sheet (Inner-state)

					// $objPHPExcel->createSheet(1);
  
					// $objPHPExcel->setActiveSheetIndex(1);

					// $objPHPExcel->getActiveSheet(1)->setTitle('Inner-state');
					
					$temp_heading_ind = $out_excel_ind+1;

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$temp_heading_ind, 'Details of Inter-State(Out State) Outward Supply to Unregistered Person : Outstate Namewise - Without GST No');

					$objPHPExcel->setActiveSheetIndex(0)->mergeCells("B$temp_heading_ind:G$temp_heading_ind");
					$objPHPExcel->getActiveSheet(0)->getStyle("B$temp_heading_ind:G$temp_heading_ind")->applyFromArray($titleStyleArray);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$out_excel_ind+3, '#');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$out_excel_ind+3, 'Place of Supply (State/UT)');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$out_excel_ind+3, 'Total Taxable ValuePlace');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$out_excel_ind+3, 'Amount of Integrated TaxTotal');

					$temp_ind = $out_excel_ind+3;

					$objPHPExcel->getActiveSheet(0)->getStyle("B$temp_ind:E$temp_ind")->applyFromArray($headerStyleArray);

					$inner_excel_ind = $out_excel_ind + 4;
					foreach ($data["innerState"] as $otw) 
					{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$inner_excel_ind, $otw["perticular"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$inner_excel_ind, $otw["placeOfSupply"]);
 
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$inner_excel_ind, $otw["totalTax"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$inner_excel_ind, $otw["integratedTax"]);

						$inner_excel_ind++;
					}

					//heading-end
					// $borderCount = "P".($loopCount+4-1);

					// $objPHPExcel->getActiveSheet(1)->getStyle("B3:E3")->applyFromArray($headerStyleArray);
					

					//create 3rd sheet (Inward)

					// $objPHPExcel->createSheet(2);

					// $objPHPExcel->setActiveSheetIndex(2);

					// $objPHPExcel->getActiveSheet(2)->setTitle('Inward');

					$temp_heading_ind = $inner_excel_ind+1;

					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$temp_heading_ind, 'Details of Inward Supply (Purchase) : (With GST No and Without GST No) : Taxable and Zero Tax Purchase');

					$objPHPExcel->setActiveSheetIndex(0)->mergeCells("B$temp_heading_ind:G$temp_heading_ind");
					$objPHPExcel->getActiveSheet(0)->getStyle("B$temp_heading_ind:G$temp_heading_ind")->applyFromArray($titleStyleArray);


					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$inner_excel_ind + 3, 'Number of Supplies');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$inner_excel_ind + 3, 'Total Taxable Value');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$inner_excel_ind + 3, 'Integrated Tax');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$inner_excel_ind + 3, 'Central Tax');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$inner_excel_ind + 3, 'State/UT Tax');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$inner_excel_ind + 3, 'Cess');

					$temp_ind = $inner_excel_ind+3;

					$objPHPExcel->getActiveSheet(0)->getStyle("B$temp_ind:G$temp_ind")->applyFromArray($headerStyleArray);

					$in_excel_ind = $inner_excel_ind + 4;
					foreach ($data["inward"] as $otw) 
					{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$in_excel_ind, $otw["perticular"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$in_excel_ind, $otw["totalTax"]);
 
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$in_excel_ind, $otw["igst"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$in_excel_ind, $otw["cgst"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$in_excel_ind, $otw["sgst"]);

						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$in_excel_ind, $otw["cess"]);

						$in_excel_ind++;
					}

					//heading-end
					// $objPHPExcel->getActiveSheet(2)->getStyle("B3:G3")->applyFromArray($headerStyleArray);
					
				/* Sheet End */

				// style for header

				// style for Title

				

				
				// $objPHPExcel->setActiveSheetIndex(0);

				// set header style

				// $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($headerStyleArray);

				// $objPHPExcel->getActiveSheet()->getStyle('B2:M2')->applyFromArray($headerStyleArray);

				//$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($headerStyleArray);

				// $objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($headerStyleArray);

				

				// set title style

				// $objPHPExcel->getActiveSheet()->getStyle('B2:J2')->applyFromArray($titleStyleArray);

				

				// make unique name

				$dateTime = date("d-m-Y h-i-s");

				$convertedDateTime = str_replace(" ","-",$dateTime);

				$splitDateTime = explode("-",$convertedDateTime);

				$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];

				$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx

				$path = $constantArray['saleTaxUrl'];

				$documentPathName = $path.$documentName;



				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

				$objWriter->save($documentPathName);

				

				$pathArray = array();

				$pathArray['documentPath'] = $documentPathName;
				
				return $pathArray;

			}

			else

			{

				$jsonEncodedData = json_encode($data);

				return $jsonEncodedData;

			}

		}

		else

		{

			$jsonEncodedData = json_encode($data);

			return $jsonEncodedData;

		}
	}

}