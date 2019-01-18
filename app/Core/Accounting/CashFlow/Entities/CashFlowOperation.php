<?php
namespace ERP\Core\Accounting\CashFlow\Entities;

use mPDF;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Companies\Services\CompanyService;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Alignment;
use Carbon;
use stdclass;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CashFlowOperation extends CompanyService
{
	/**
	 * calculate given data and set it into the pdf file 
	 * $param database data
	 * @return the array of document-path/exception message
	*/
	public function generatePdf($data)
	{
		$decodedData = json_decode($data);
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$headerPart = "<table style='border: 1px solid black; width:100%'>
						<thead style='border: 1px solid black; width:100%;'>
							<tr style='border: 1px solid black;width:100%;'>
								<th style='border: 1px solid black; width:50%;'>Ledger-Name</th>
								<th style='border: 1px solid black;width:25%;'>Debit</th>
								<th style='border: 1px solid black;width:25%;'>Credit</th>
							</tr>
						</thead><tbody>";
		$bodyPart = "";
		$creditAmountTotal = 0;
		$debitAmountTotal = 0;
		$cashFlow = new CashFlowOperation();
		$companyDetail = $cashFlow->getCompanyData($decodedData[0]->ledger->companyId);
		$decodedCompanyData = json_decode($companyDetail);
		
		//calculating current accounting-year
		$mytime = Carbon\Carbon::now();
		$currentDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $mytime)->format('Y-m-d');
		$dateTime = $mytime->toDateTimeString();
		$yearStartDate = $mytime->year.'-04-01 00:00:00';
		if($dateTime >= $yearStartDate)
		{
			$year = $mytime->year+1;
		}
		else
		{
			$year = $mytime->year+1;
		}
		$heading = 	'<div style="text-align: center; font-weight: bold; font-size:20px;">'.$decodedCompanyData->companyName.'</div>
					<div style="text-align: center; font-weight: bold; font-size:15px;">Statement of Cash Flows</div>
					<div style="text-align: center; font-weight: bold; font-size:15px;">For the Year Ended March 31,'.$year.'</div>';
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			
			if(strcmp($decodedData[$arrayData]->amountType,"credit")==0)
			{
				$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
									<td style='border: 1px solid black; width:50%;'>".$decodedData[$arrayData]->ledger->ledgerName."</td>
									<td style='border: 1px solid black;width:25%; text-align:center;'> - </td>
									<td style='border: 1px solid black; width:25%;text-align:center;'>".$decodedData[$arrayData]->amount."</td></tr>";
				$creditAmountTotal = $creditAmountTotal+$decodedData[$arrayData]->amount;
			}
			else
			{
				$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
									<td style='border: 1px solid black; width:50%;'>".$decodedData[$arrayData]->ledger->ledgerName."</td>
									<td style='border: 1px solid black;width:25%; text-align:center;'>".$decodedData[$arrayData]->amount."</td>
									<td style='border: 1px solid black; width:25%;text-align:center;'> - </td></tr>";
				$debitAmountTotal = $debitAmountTotal+$decodedData[$arrayData]->amount;
			}
		}
		if($debitAmountTotal>$creditAmountTotal)
		{
			$differenceDr = number_format(($debitAmountTotal-$creditAmountTotal),$decodedCompanyData->noOfDecimalPoints);
			$differenceCr = '';
		}
		else
		{
			$differenceCr = number_format(($creditAmountTotal-$debitAmountTotal),$decodedCompanyData->noOfDecimalPoints);
			$differenceDr = '';
		}
		$debitAmountTotal = number_format($debitAmountTotal,$decodedCompanyData->noOfDecimalPoints);
		$creditAmountTotal = number_format($creditAmountTotal,$decodedCompanyData->noOfDecimalPoints);
		
		$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
							<td style='border: 1px solid black; width:50%;'>Total</td>
							<td style='border: 1px solid black; width:25%;text-align:center;'>".$debitAmountTotal."</td>
							<td style='border: 1px solid black;width:25%; text-align:center;'>".$creditAmountTotal."</td></tr>
							<tr style='border: 1px solid black;'>
							<td style='border: 1px solid black; width:50%;'>Difference</td>
							<td style='border: 1px solid black; width:25%;text-align:center;'>".$differenceDr."</td>
							<td style='border: 1px solid black;width:25%; text-align:center;'>".$differenceCr."</td></tr>";
		$footerPart = "</tbody></table>";
		$htmlBody = $heading.$headerPart.$bodyPart.$footerPart;
		
		//generate pdf
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		
		$path = $constantArray['cashFlowPdf'];
		
		$documentPathName = $path.$documentName;
		$mpdf = new mPDF('A4','landscape');
		// $mpdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold; font-size:20px;">Cash Flow</div>');
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->WriteHTML($htmlBody);
		$mpdf->Output($documentPathName,'F');
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	/**
	 * calculate given data and set it into the pdf file 
	 * $param database data
	 * @return the array of document-path/exception message
	*/
	public function generateTwoSidePdf($data)
	{
		$decodedData = json_decode($data);
		
		$cashFlow = new CashFlowOperation();
		$companyDetail = $cashFlow->getCompanyData($decodedData[0]->ledger->companyId);
		$decodedCompanyData = json_decode($companyDetail);
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$headerPart = "<table style='border: 1px solid black; width:100%'>
						<thead style='border: 1px solid black; width:100%;'>
							<tr style='border: 1px solid black;width:100%;'>
								<th style='border: 1px solid black; width:50%;'>Ledger-Name</th>
								<th style='border: 1px solid black; width:50%;'>Amount</th>
								<th style='border: 1px solid black;width:25%;'>Ledger-Name</th>
								<th style='border: 1px solid black;width:25%;'>Amount</th>
							</tr>
						</thead><tbody>";			
		$calculatedData = $this->getCalculatedData($decodedData);
		$decodedData = $calculatedData['arrayData'];
		$bodyPart = "";
		
		//calculating current accounting-year
		$mytime = Carbon\Carbon::now();
		$currentDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $mytime)->format('Y-m-d');
		$dateTime = $mytime->toDateTimeString();
		$yearStartDate = $mytime->year.'-04-01 00:00:00';
		if($dateTime >= $yearStartDate)
		{
			$year = $mytime->year+1;
		}
		else
		{
			$year = $mytime->year+1;
		}
		$heading = 	'<div style="text-align: center; font-weight: bold; font-size:20px;">'.$decodedCompanyData->companyName.'</div>
					<div style="text-align: center; font-weight: bold; font-size:15px;">Statement of Cash Flows</div>
					<div style="text-align: center; font-weight: bold; font-size:15px;">For the Year Ended March 31,'.$year.'</div>';
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			if(count($decodedData[$arrayData])==2)
			{
				$bodyPart = $bodyPart."<tr style='border: 1px solid black;'>
									<td style='border: 1px solid black; width:50%;'>".$decodedData[$arrayData][1]->ledgerName."</td>
									<td style='border: 1px solid black;width:25%; text-align:center;'>".$decodedData[$arrayData][1]->debitAmount."</td>
									<td style='border: 1px solid black; width:25%;text-align:center;'>".$decodedData[$arrayData][0]->ledgerName."</td>
									<td style='border: 1px solid black; width:25%;text-align:center;'>".$decodedData[$arrayData][0]->creditAmount."</td></tr>";
			}
			else
			{
				if(array_key_exists("0",$decodedData[$arrayData]))
				{
					$bodyPart = $bodyPart."<tr style='border: 1px solid black;'>
									<td style='border: 1px solid black; width:50%;'></td>
									<td style='border: 1px solid black;width:25%; text-align:center;'></td>
									<td style='border: 1px solid black; width:25%;text-align:center;'>".$decodedData[$arrayData][0]->ledgerName."</td>
									<td style='border: 1px solid black; width:25%;text-align:center;'>".$decodedData[$arrayData][0]->creditAmount."</td></tr>";
				}
				else
				{
					$bodyPart = $bodyPart."<tr style='border: 1px solid black;'>
									<td style='border: 1px solid black; width:50%;'>".$decodedData[$arrayData][1]->ledgerName."</td>
									<td style='border: 1px solid black;width:25%; text-align:center;'>".$decodedData[$arrayData][1]->debitAmount."</td>
									<td style='border: 1px solid black; width:25%;text-align:center;'></td>
									<td style='border: 1px solid black; width:25%;text-align:center;'></td></tr>";
				}
			}
		}
		
		if($calculatedData['totalDebit']>$calculatedData['totalCredit'])
		{
			$differenceDr = number_format(($calculatedData['totalDebit']-$calculatedData['totalCredit']),$decodedCompanyData->noOfDecimalPoints);
			$differenceCr = "-";
		}
		else
		{
			$differenceCr = number_format(($calculatedData['totalCredit']-$calculatedData['totalDebit']),$decodedCompanyData->noOfDecimalPoints);
			$differenceDr = "-";
		}
		
		$calculatedData['totalDebit'] = number_format($calculatedData['totalDebit'],$decodedCompanyData->noOfDecimalPoints);
		$calculatedData['totalCredit'] = number_format($calculatedData['totalCredit'],$decodedCompanyData->noOfDecimalPoints);
		$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
									<td style='border: 1px solid black; width:50%;'>Total</td>
									<td style='border: 1px solid black;width:25%; text-align:center;'>".$calculatedData['totalDebit']."</td>
									<td style='border: 1px solid black; width:25%;text-align:center;'>Total</td>;
									<td style='border: 1px solid black; width:25%;text-align:center;'>".$calculatedData['totalCredit']."</td></tr>";
		$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
									<td style='border: 1px solid black; width:50%;'>Difference</td>
									<td style='border: 1px solid black;width:25%; text-align:center;'>".$differenceDr."</td>
									<td style='border: 1px solid black; width:25%;text-align:center;'>Difference</td>;
									<td style='border: 1px solid black; width:25%;text-align:center;'>".$differenceCr."</td></tr>";
		
		$footerPart = "</tbody></table>";
		$htmlBody = $heading.$headerPart.$bodyPart.$footerPart;
		
		// generate pdf
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		
		$path = $constantArray['cashFlowPdf'];
		
		$documentPathName = $path.$documentName;
		$mpdf = new mPDF('A4','landscape');
		// $mpdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold; font-size:20px;">Cash Flow</div>');
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->WriteHTML($htmlBody);
		$mpdf->Output($documentPathName,'F');
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	/**
	 * calculate given data and set it into the excel file 
	 * $param database data
	 * @return the array of document-path/exception message
	*/
	public function generateExcel($data)
	{
		//decode the database data
		$decodedData = json_decode($data);
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$companyService = new CashFlowOperation();
		$companyDetail = $companyService->getCompanyData($decodedData[0]->ledger->companyId);
		$decodedCompanyData = json_decode($companyDetail);
		
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
		$objPHPExcel->getActiveSheet()->setTitle('CashFLow');
		
		//calculating current accounting-year
		$mytime = Carbon\Carbon::now();
		$currentDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $mytime)->format('Y-m-d');
		$dateTime = $mytime->toDateTimeString();
		$yearStartDate = $mytime->year.'-04-01 00:00:00';
		if($dateTime >= $yearStartDate)
		{
			$year = $mytime->year+1;
		}
		else
		{
			$year = $mytime->year+1;
		}
				
		//heading-start
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,1, $decodedCompanyData->companyName);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,2, 'Statement of Cash Flows');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,3, 'For the Year Ended March 31,'.$year);
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,4, 'Ledger-Name');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,4, 'Debit');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,4, 'Credit');
		//heading-end
		$creditAmountTotal=0;
		$debitAmountTotal=0;
		
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			if(strcmp($decodedData[$arrayData]->amountType,"credit")==0)
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+5,$decodedData[$arrayData]->ledger->ledgerName);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+5,'-');
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$arrayData+5,$decodedData[$arrayData]->amount);
				$creditAmountTotal = $creditAmountTotal+$decodedData[$arrayData]->amount;
			}
			else
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+5,$decodedData[$arrayData]->ledger->ledgerName);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+5,$decodedData[$arrayData]->amount);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$arrayData+5,'-');
				$debitAmountTotal = $debitAmountTotal+$decodedData[$arrayData]->amount;
			}
		}
		if($debitAmountTotal>$creditAmountTotal)
		{
			$differenceDr = $debitAmountTotal-$creditAmountTotal;
			$differenceCr = '';
		}
		else
		{
			$differenceCr = $creditAmountTotal-$debitAmountTotal;
			$differenceDr = '';
		}
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,count($decodedData)+5,'Total');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,count($decodedData)+5,$debitAmountTotal);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,count($decodedData)+5,$creditAmountTotal);
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,count($decodedData)+6,'Difference');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,count($decodedData)+6,$differenceDr);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,count($decodedData)+6,$differenceCr);
		
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
			'color' => array('rgb' => '#00000'),
			'size'  => 13,
			'name'  => 'Verdana'
		));
		
		// set header style
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->applyFromArray($headerStyleArray);
		
		$decimalPoints = $this->setDecimalPoint($decodedCompanyData->noOfDecimalPoints);
		
		$bSaveDynamicRow = "B".(count($decodedData)+6);
		$cSaveDynamicRow = "C".(count($decodedData)+6);
		
		$objPHPExcel->getActiveSheet()->getStyle("B5:".$bSaveDynamicRow)->getNumberFormat()->setFormatCode($decimalPoints);
		$objPHPExcel->getActiveSheet()->getStyle("C5:".$cSaveDynamicRow)->getNumberFormat()->setFormatCode($decimalPoints);
		
		// set title style
		$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($titleStyleArray);
		
		// make unique name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx
		$path = $constantArray['cashFlowExcel'];
		$documentPathName = $path.$documentName;
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($documentPathName);
		
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	/**
	 * calculate given data and set it into the excel file 
	 * $param database data
	 * @return the array of document-path/exception message
	*/
	public function generateTwoSideExcel($data)
	{
		//decode the database data
		$decodedData = json_decode($data);
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$companyService = new CashFlowOperation();
		$companyDetail = $companyService->getCompanyData($decodedData[0]->ledger->companyId);
		$decodedCompanyData = json_decode($companyDetail);
		
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
		$objPHPExcel->getActiveSheet()->setTitle('CashFLow');
		
		$calculatedData = $this->getCalculatedData($decodedData);
		$decodedData = $calculatedData['arrayData'];
		
		//calculating current accounting-year
		$mytime = Carbon\Carbon::now();
		$currentDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $mytime)->format('Y-m-d');
		$dateTime = $mytime->toDateTimeString();
		$yearStartDate = $mytime->year.'-04-01 00:00:00';
		if($dateTime >= $yearStartDate)
		{
			$year = $mytime->year+1;
		}
		else
		{
			$year = $mytime->year+1;
		}
				
		//heading-start
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,1, $decodedCompanyData->companyName);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,2, 'Statement of Cash Flows');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,3, 'For the Year Ended March 31,'.$year);
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B1:C1');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:C2');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:C3');
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,4, 'Ledger-Name');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,4, 'Amount');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,4, 'Ledger-Name');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,4, 'Amount');
		//heading-end
		$creditAmountTotal=0;
		$debitAmountTotal=0;
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			if(count($decodedData[$arrayData])==2)
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+5,$decodedData[$arrayData][1]->ledgerName);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+5,$decodedData[$arrayData][1]->debitAmount);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$arrayData+5,$decodedData[$arrayData][0]->ledgerName);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$arrayData+5,$decodedData[$arrayData][0]->creditAmount);
			}
			else
			{
				if(array_key_exists("0",$decodedData[$arrayData]))
				{
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+5,'');
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+5,'');
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$arrayData+5,$decodedData[$arrayData][0]->ledgerName);
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$arrayData+5,$decodedData[$arrayData][0]->creditAmount);
				}
				else
				{
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+5,$decodedData[$arrayData][1]->ledgerName);
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+5,$decodedData[$arrayData][1]->debitAmount);
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$arrayData+5,'');
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$arrayData+5,'');
				}
			}
		}
		if($calculatedData['totalDebit']>$calculatedData['totalCredit'])
		{
			$differenceDr = $calculatedData['totalDebit']-$calculatedData['totalCredit'];
			$differenceCr = "-";
		}
		else
		{
			$differenceCr = $calculatedData['totalCredit']-$calculatedData['totalDebit'];
			$differenceDr = "-";
		}
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,count($decodedData)+5,'Total');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,count($decodedData)+5,$calculatedData['totalDebit']);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,count($decodedData)+5,'Total');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,count($decodedData)+5,$calculatedData['totalCredit']);
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,count($decodedData)+6,'Difference');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,count($decodedData)+6,$differenceDr);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,count($decodedData)+6,'Difference');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,count($decodedData)+6,$differenceCr);
		
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
			'color' => array('rgb' => '#00000'),
			'size'  => 13,
			'name'  => 'Verdana'
		));
		
		// set header style
		$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->applyFromArray($headerStyleArray);
		
		$decimalPoints = $this->setDecimalPoint($decodedCompanyData->noOfDecimalPoints);
		
		$bSaveDynamicRow = "B".(count($decodedData)+6);
		$dSaveDynamicRow = "D".(count($decodedData)+6);
		
		$objPHPExcel->getActiveSheet()->getStyle("B5:".$bSaveDynamicRow)->getNumberFormat()->setFormatCode($decimalPoints);
		$objPHPExcel->getActiveSheet()->getStyle("D5:".$dSaveDynamicRow)->getNumberFormat()->setFormatCode($decimalPoints);
		
		// set title style
		$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($titleStyleArray);
		
		// make unique name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx
		$path = $constantArray['cashFlowExcel'];
		$documentPathName = $path.$documentName;
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($documentPathName);
		
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	/**
	 * calculate given data and returns the result
	 * $param database decoded-data
	 * @return the array-result
	*/
	public function getCalculatedData($decodedData)
	{
		$trialBalanceArray = array();
		$totalDebit = 0;
		$totalCredit = 0;
		$dataLength = count($decodedData)-1;
		
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++) 
		{
			$dataOfTrial = $decodedData[$arrayData];
			$innerArray = array();
			$trialObject = new stdclass();
			$trialObject->ledgerId = $dataOfTrial->ledger->ledgerId;
			$trialObject->ledgerName = $dataOfTrial->ledger->ledgerName;
			$trialObject->amountType = $dataOfTrial->amountType;
		
			if($dataOfTrial->amountType == 'debit')
            {
				$trialObject->debitAmount = $dataOfTrial->amount;
				$trialObject->creditAmount = "-";
				$totalDebit +=$dataOfTrial->amount;
				$cntLen = count($trialBalanceArray);
				if($cntLen > 0)
				{
					$inFlag = 0;
					for($innerLoop=0;$innerLoop<$cntLen;$innerLoop++)
					{
						if(!array_key_exists("1",$trialBalanceArray[$innerLoop]))
						{	
							$inFlag = 1;
							$trialBalanceArray[$innerLoop][1] = $trialObject;
							break;
						}
					}
					if($inFlag == 0)
					{
						$innerArray[1] = $trialObject;
						array_push($trialBalanceArray,$innerArray);
					}
				}
				else
				{
					$innerArray[1] = $trialObject;
					array_push($trialBalanceArray,$innerArray);
				}
			}
			else
			{
				$trialObject->debitAmount = "-";
				$trialObject->creditAmount = $dataOfTrial->amount;
				
				$totalCredit += $dataOfTrial->amount;
				
				$cntLen = count($trialBalanceArray);
				if($cntLen > 0)
				{
					$inFlag = 0;
					for($innerLoop=0;$innerLoop<$cntLen;$innerLoop++)
					{
						if(!array_key_exists("0",$trialBalanceArray[$innerLoop]))
						{
							$inFlag = 1;
							$trialBalanceArray[$innerLoop][0] = $trialObject;
							break;
						}
					}
					if($inFlag == 0)
					{
						$innerArray[0] = $trialObject;
						array_push($trialBalanceArray,$innerArray);
					}
				}
				else
				{
					
					$innerArray[0] = $trialObject;
					array_push($trialBalanceArray,$innerArray);
				}
			}
		}
		$finalArray = array();
		$finalArray['totalCredit'] = $totalCredit;
		$finalArray['totalDebit'] = $totalDebit;
		$finalArray['arrayData'] = $trialBalanceArray;
		return $finalArray;
	}
	
	/**
	 * calculate the decimal point
	 * $param decimal-point
	*/
	public function setDecimalPoint($decimalPoint)
	{
		if($decimalPoint==1)
		{
			return "0.0";
		}
		else if($decimalPoint==2)
		{
			return "0.00";
		}
		else if($decimalPoint==3)
		{
			return "0.000";
		}
		else if($decimalPoint==4)
		{
			return "0.0000";
		}
	}
}