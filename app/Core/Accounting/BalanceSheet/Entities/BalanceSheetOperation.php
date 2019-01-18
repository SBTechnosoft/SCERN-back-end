<?php
namespace ERP\Core\Accounting\BalanceSheet\Entities;

use mPDF;
use ERP\Entities\Constants\ConstantClass;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Alignment;
use Carbon;
use ERP\Core\Companies\Services\CompanyService;
use stdclass;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BalanceSheetOperation extends ConstantClass
{
	/**
	 * calculate given data and set it into the pdf file 
	 * $param database data
	 * @return the array of document-path/exception message
	*/
	public function generateTwoSidePdf($data)
	{
		//decode the database data
		$decodedData = json_decode($data);
		$constantClass = new BalanceSheetOperation();
		$constantArray = $constantClass->constantVariable();
		
		//make a header for pdf
		$headerPart = "<table style='border: 1px solid black; width:100%'>
						<thead style='border: 1px solid black; width:100%;'>
							<tr style='border: 1px solid black;width:100%;'>
								<th style='border: 1px solid black; width:50%;'>Assets</th>
								<th style='border: 1px solid black;width:25%;'>Amount</th>
								<th style='border: 1px solid black;width:25%;'>Liabilities</th>
								<th style='border: 1px solid black;width:25%;'>Amount</th>
							</tr>
						</thead><tbody>";
		
		//calculate data
		$balanceSheetArrayData = $this->getCalculatedData($decodedData);
		$balanceArray = $balanceSheetArrayData['arrayData'];
		
		$companyService = new CompanyService();
		$companyDetail = $companyService->getCompanyData($decodedData[0]->ledger->companyId);
		$decodedCompanyData = json_decode($companyDetail);
		$mytime = Carbon\Carbon::now();
		$currentDate = "At ".$mytime->toFormattedDateString();
		
		$heading = 	'<div style="text-align: center; font-weight: bold; font-size:20px;">'.$decodedCompanyData->companyName.'</div>
					<div style="text-align: center; font-weight: bold; font-size:15px;">Balance Sheet</div>
					<div style="text-align: center; font-weight: bold; font-size:15px;">'.$currentDate.'</div>';
		
		//make a table and set data for pdf
		$bodyPart = "";
		for($balanceSheetArray=0;$balanceSheetArray<count($balanceArray);$balanceSheetArray++)
		{
			if(count($balanceArray[$balanceSheetArray])==2)
			{
				$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
					  <td style='border: 1px solid black; width:50%; text-align:center;'>".$balanceArray[$balanceSheetArray][0]->ledgerName."</td>
					  <td style='border: 1px solid black;width:25%; text-align:center;'>".$balanceArray[$balanceSheetArray][0]->creditAmount."</td>
					  <td style='border: 1px solid black;width:25%; text-align:center;'>".$balanceArray[$balanceSheetArray][1]->ledgerName."</td>
					  <td style='border: 1px solid black;width:25%; text-align:center;'>".$balanceArray[$balanceSheetArray][1]->debitAmount."</td>";
			}
			else
			{
				if(array_key_exists('0',$balanceArray[$balanceSheetArray]))
				{
					$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
						  <td style='border: 1px solid black; width:50%; text-align:center;'>".$balanceArray[$balanceSheetArray][0]->ledgerName."</td>
						  <td style='border: 1px solid black;width:25%; text-align:center;'>".$balanceArray[$balanceSheetArray][0]->creditAmount."</td>
						  <td style='border: 1px solid black;width:25%; text-align:center;'></td>
						  <td style='border: 1px solid black;width:25%; text-align:center;'></td>";
				}
				else
				{
					$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
						  <td style='border: 1px solid black; width:50%; text-align:center;'></td>
						  <td style='border: 1px solid black;width:25%; text-align:center;'></td>
						  <td style='border: 1px solid black;width:25%; text-align:center;'>".$balanceArray[$balanceSheetArray][1]->ledgerName."</td>
						  <td style='border: 1px solid black;width:25%; text-align:center;'>".$balanceArray[$balanceSheetArray][1]->debitAmount."</td>";
				}
			}
		}
		$balanceSheetArrayData['totalCredit'] = number_format($balanceSheetArrayData['totalCredit'],$decodedCompanyData->noOfDecimalPoints);
		$balanceSheetArrayData['totalDebit'] = number_format($balanceSheetArrayData['totalDebit'],$decodedCompanyData->noOfDecimalPoints);
		
		$bodyPart = $bodyPart."<tr style='border: 1px solid black;'>
		<td style='border: 1px solid black; width:50%; text-align:center;'>Total Assets </td>
		<td style='border: 1px solid black;width:25%; text-align:center;'>".$balanceSheetArrayData['totalCredit']."</td>
		<td style='border: 1px solid black; width:50%; text-align:center;'>Total Liabilities & Equity</td>
		<td style='border: 1px solid black;width:25%; text-align:center;'>".$balanceSheetArrayData['totalDebit']."</td></tr>";
		$footerPart = "</tbody></table>";
		$htmlBody = $heading.$headerPart.$bodyPart.$footerPart;
		
		//generate pdf
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		
		$path = $constantArray['balanceSheetPdf'];
		
		$documentPathName = $path.$documentName;
		$mpdf = new mPDF('A4','landscape');
		// $mpdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold; font-size:20px;">Balance Sheet</div>');
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->WriteHTML($htmlBody);
		$mpdf->Output($documentPathName,'F');
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	/**
	 * calculate given data and returns the result
	 * $param database decoded-data
	 * @return the array-result
	*/
	public function generatePdf($data)
	{
		//decode the database data
		$decodedData = json_decode($data);
		$constantClass = new BalanceSheetOperation();
		$constantArray = $constantClass->constantVariable();
		
		$companyService = new CompanyService();
		$companyDetail = $companyService->getCompanyData($decodedData[0]->ledger->companyId);
		$decodedCompanyData = json_decode($companyDetail);
		
		//make a header for pdf
		$headerPart = "<table style='border: 1px solid black; width:100%'>
						<thead style='border: 1px solid black; width:100%;'>
							<tr style='border: 1px solid black;width:100%;'>
								<th style='border: 1px solid black; width:50%;'>Assets & Liabilities</th>
								<th style='border: 1px solid black;width:25%;'>Amount</th>
							</tr>
						</thead><tbody>";
		$mytime = Carbon\Carbon::now();
		$currentDate = "At ".$mytime->toFormattedDateString();
		
		$heading = 	'<div style="text-align: center; font-weight: bold; font-size:20px;">'.$decodedCompanyData->companyName.'</div>
					<div style="text-align: center; font-weight: bold; font-size:15px;">Balance Sheet</div>
					<div style="text-align: center; font-weight: bold; font-size:15px;">'.$currentDate.'</div>';
		$creditArray = array();
		$debitArray = array();
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			if(strcmp($decodedData[$arrayData]->amountType,'credit')==0)
			{
				array_push($creditArray,$decodedData[$arrayData]);
			}
			else
			{
				array_push($debitArray,$decodedData[$arrayData]);
			}
		}
		$bodyPart="";
		$creditAmountTotal=0;
		$debitAmountTotal=0;
		
		for($arrayData=0;$arrayData<count($creditArray);$arrayData++)
		{
			$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
						  <td style='border: 1px solid black; width:50%; text-align:center;'>".$creditArray[$arrayData]->ledger->ledgerName."</td>
						  <td style='border: 1px solid black;width:25%; text-align:center;'>".$creditArray[$arrayData]->amount."</td></tr>";
			$creditAmountTotal = $creditAmountTotal+$creditArray[$arrayData]->amount;
			if($arrayData == count($creditArray)-1)
			{
				$creditAmountTotal = number_format($creditAmountTotal,$decodedCompanyData->noOfDecimalPoints);
				$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'> 
					<td style='border: 1px solid black; width:50%; text-align:center;'><b>Total Assets</b></td>
					<td style='border: 1px solid black;width:25%; text-align:center;'><b>".$creditAmountTotal."</b></td>
					</tr>";
			}
		}
		for($arrayData=0;$arrayData<count($debitArray);$arrayData++)
		{
			$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
						  <td style='border: 1px solid black; width:50%; text-align:center;'>".$debitArray[$arrayData]->ledger->ledgerName."</td>
						  <td style='border: 1px solid black;width:25%; text-align:center;'>".$debitArray[$arrayData]->amount."</td></tr>";
			$debitAmountTotal = $debitAmountTotal+$debitArray[$arrayData]->amount;
			if($arrayData == count($debitArray)-1)
			{
				$debitAmountTotal = number_format($debitAmountTotal,$decodedCompanyData->noOfDecimalPoints);
				$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'> 
					<td style='border: 1px solid black; width:50%; text-align:center;'><b>Total Liabilities</b></td>
					<td style='border: 1px solid black;width:25%; text-align:center;'><b>".$debitAmountTotal."</b></td>
					</tr>";
			}
		}
		$footerPart = "</tbody></table>";
		$htmlBody = $heading.$headerPart.$bodyPart.$footerPart;
		
		//generate pdf
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		
		$path = $constantArray['balanceSheetPdf'];
		
		$documentPathName = $path.$documentName;
		$mpdf = new mPDF('A4','landscape');
		// $mpdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold; font-size:20px;">Balance Sheet</div>');
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->WriteHTML($htmlBody);
		$mpdf->Output($documentPathName,'F');
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	/**
	 * calculate given data and returns the result
	 * $param database decoded-data
	 * @return the array-result
	*/
	public function generateTwoSideExcel($data)
	{
		//decode the database data
		$decodedData = json_decode($data);
		$constantClass = new BalanceSheetOperation();
		$constantArray = $constantClass->constantVariable();
		
		//calculate data
		$balanceSheetArrayData = $this->getCalculatedData($decodedData);
		$balanceArray = $balanceSheetArrayData['arrayData'];
		
		$companyService = new CompanyService();
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
		$objPHPExcel->getActiveSheet()->setTitle('BalanceSheet');
		
		$mytime = Carbon\Carbon::now();
		$currentDate = "At ".$mytime->toFormattedDateString();
	
		//heading-start
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,1, $decodedCompanyData->companyName);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,2, 'Balance Sheet');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,3, $currentDate);
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B1:C1');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:C2');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:C3');
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,4, 'Assets');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,4, 'Amount');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,4, 'Liabilities');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,4, 'Amount');
		//heading-end
		
		//set data into excel-sheet
		for($balanceSheetArray=0;$balanceSheetArray<count($balanceArray);$balanceSheetArray++)
		{
			if(count($balanceArray[$balanceSheetArray])==2)
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$balanceSheetArray+5,$balanceArray[$balanceSheetArray][0]->ledgerName);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$balanceSheetArray+5,$balanceArray[$balanceSheetArray][0]->creditAmount);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$balanceSheetArray+5,$balanceArray[$balanceSheetArray][1]->ledgerName);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$balanceSheetArray+5,$balanceArray[$balanceSheetArray][1]->debitAmount);
			}
			else
			{
				if(array_key_exists('0',$balanceArray[$balanceSheetArray]))
				{
					
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$balanceSheetArray+5,$balanceArray[$balanceSheetArray][0]->ledgerName);
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$balanceSheetArray+5,$balanceArray[$balanceSheetArray][0]->creditAmount);
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$balanceSheetArray+5,'');
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$balanceSheetArray+5,'');
				}
				else
				{
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$balanceSheetArray+5,'');
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$balanceSheetArray+5,'');
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$balanceSheetArray+5,$balanceArray[$balanceSheetArray][1]->ledgerName);
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$balanceSheetArray+5,$balanceArray[$balanceSheetArray][1]->debitAmount);
				}
			}
		}
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,count($balanceArray)+5,'Total Assets');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,count($balanceArray)+5,$balanceSheetArrayData['totalCredit']);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,count($balanceArray)+5,'Total Liabilities');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,count($balanceArray)+5,$balanceSheetArrayData['totalDebit']);
		
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
		
		$bSaveDynamicRow = "B".(count($balanceArray)+5);
		$dSaveDynamicRow = "D".(count($balanceArray)+5);
		
		$objPHPExcel->getActiveSheet()->getStyle("B5:".$bSaveDynamicRow)->getNumberFormat()->setFormatCode($decimalPoints);
		$objPHPExcel->getActiveSheet()->getStyle("D5:".$dSaveDynamicRow)->getNumberFormat()->setFormatCode($decimalPoints);
		
		// set title style
		$objPHPExcel->getActiveSheet()->getStyle('B1:C1')->applyFromArray($titleStyleArray);
		
		// make unique name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx
		$path = $constantArray['balanceSheetExcel'];
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
	public function generateExcel($data)
	{
		//decode the database data
		$decodedData = json_decode($data);
		$constantClass = new BalanceSheetOperation();
		$constantArray = $constantClass->constantVariable();
		
		$companyService = new CompanyService();
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
		$objPHPExcel->getActiveSheet()->setTitle('BalanceSheet');
		
		$mytime = Carbon\Carbon::now();
		$currentDate = "At ".$mytime->toFormattedDateString();
					
		//heading-start
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,1, $decodedCompanyData->companyName);
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,2, 'Balance Sheet');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,3, $currentDate);
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:B1');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:B2');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:B3');
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,4, 'Assets & Liabilities');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,4, 'Amount');
		//heading-end
		
		$creditArray = array();
		$debitArray = array();
		//set data into excel-sheet
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			if(strcmp($decodedData[$arrayData]->amountType,'credit')==0)
			{
				array_push($creditArray,$decodedData[$arrayData]);
			}
			else
			{
				array_push($debitArray,$decodedData[$arrayData]);
			}
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
			'color' => array('rgb' => '#00000'),
			'size'  => 13,
			'name'  => 'Verdana'
		));
		$creditAmountTotal=0;
		$debitAmountTotal=0;
		for($arrayData=0;$arrayData<count($creditArray);$arrayData++)
		{
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+5,$creditArray[$arrayData]->ledger->ledgerName);
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+5,$creditArray[$arrayData]->amount);
			$creditAmountTotal = $creditAmountTotal+$creditArray[$arrayData]->amount;
			
			if($arrayData == count($creditArray)-1)
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+6,"Total Assets");
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+6,$creditAmountTotal);
				$aCount = "A".($arrayData+6);
				$bCount = "B".($arrayData+6);
				$objPHPExcel->getActiveSheet()->getStyle($aCount.":".$bCount)->applyFromArray($headerStyleArray);
			}
		}
		$totalCreditRow = count($creditArray)+1;
		
		for($arrayData=0;$arrayData<count($debitArray);$arrayData++)
		{
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$totalCreditRow+$arrayData+5,$debitArray[$arrayData]->ledger->ledgerName);
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$totalCreditRow+$arrayData+5,$debitArray[$arrayData]->amount);
			$debitAmountTotal = $debitAmountTotal+$debitArray[$arrayData]->amount;
			if($arrayData == count($debitArray)-1)
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$totalCreditRow+$arrayData+6,"Total Liabilities");
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$totalCreditRow+$arrayData+6,$debitAmountTotal);
				$aCount = "A".($totalCreditRow+$arrayData+6);
				$bCount = "B".($totalCreditRow+$arrayData+6);
				$objPHPExcel->getActiveSheet()->getStyle($aCount.":".$bCount)->applyFromArray($headerStyleArray);
			}
		}
		// set header style
		$objPHPExcel->getActiveSheet()->getStyle('A4:B4')->applyFromArray($headerStyleArray);
		
		$decimalPoints = $this->setDecimalPoint($decodedCompanyData->noOfDecimalPoints);
		
		$bSaveDynamicRow = "B".(count($creditArray)+count($debitArray)+4);
		// $cSaveDynamicRow = "C".(count($creditArray)+count($debitArray)+2);
		
		$objPHPExcel->getActiveSheet()->getStyle("B5:".$bSaveDynamicRow)->getNumberFormat()->setFormatCode($decimalPoints);
		// $objPHPExcel->getActiveSheet()->getStyle("C3:".$cSaveDynamicRow)->getNumberFormat()->setFormatCode($decimalPoints);
		
		// set title style
		$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($titleStyleArray);
		
		// make unique name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx
		$path = $constantArray['balanceSheetExcel'];
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