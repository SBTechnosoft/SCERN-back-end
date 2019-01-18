<?php
namespace ERP\Core\Products\Entities;

use mPDF;
use stdClass;
use ERP\Entities\Constants\ConstantClass;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Alignment;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class StockManageMpdf extends ConstantClass
{
	/**
     * get the specified resource and calculate stock-data
     * @param product array-data
	 * @return document-path 
     */
	public function calculateBalance($data)
	{
		$decodedData = json_decode($data);
		$balanceArray = array();
		$balance = array();
		
		$constantClass = new StockManageMpdf();
		$constantArray = $constantClass->constantVariable();
		$decodedData = $decodedData[0];
		
		$decodedData = $this->calculateStockManage($decodedData);
		$headerPart = "<table style='border: 1px solid black; width:100%'>
			<thead style='border: 1px solid black;'>
				<tr style='border: 1px solid black;'>
					<th  colspan='4' style='border: 1px solid black;'>Inward/Outward</th>
					
					<th  colspan='3' style='border: 1px solid black;'>Balance</th>
				</tr>
				<tr>
					<th style='border: 1px solid black;'>Dt.</th>
					<th style='border: 1px solid black;'>Sale/purchase</th>
					<th style='border: 1px solid black;'>Qty</th>
					<th style='border: 1px solid black;'>Amount</th>
					
					<th style='border: 1px solid black;'>Dt.</th>
					<th style='border: 1px solid black;'>Qty</th>
					<th style='border: 1px solid black;'>Amount</th>
				</tr>
			</thead>";
			
		$footerPart = '</table>';
		$bodyPart="";
		$mainPart = "";
		$middleOne = "";
		$loopPart="";
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			$balanceCount = count($decodedData[$arrayData]->balance);
			$ifCondition="";
			if($balanceCount==0)
			{
				$balanceCount=1;
			}
			
			if(strcmp($decodedData[$arrayData]->transactionType,'Inward')==0)
			{
				$transactionType = "purchase";
			}
			else
			{
				$transactionType = "sales";
			}
			$bodyPart =
				"<tbody>
					<tr style='background-color: white; border: 1px solid black;'>
				";
				
				if($arrayData==0)
				{
					$ifCondition = 
						"<td rowspan='".$balanceCount."' style='border: 1px solid black;'>".$decodedData[0]->transactionDate."</td>
						<td rowspan='".$balanceCount."' style='border: 1px solid black;'>".$transactionType."</td>
						<td rowspan='".$balanceCount."' style='border: 1px solid black;'>".$decodedData[0]->qty."</td>
						<td rowspan='".$balanceCount."' style='border: 1px solid black;'>".$decodedData[0]->price."</td>";
				}
				else
				{
					$ifCondition = 
						"<td rowspan='".$balanceCount."' style='border: 1px solid black;'>".$decodedData[$arrayData]->transactionDate."</td>
						<td rowspan='".$balanceCount."' style='border: 1px solid black;'>".$transactionType."</td>
						<td rowspan='".$balanceCount."' style='border: 1px solid black;'>".$decodedData[$arrayData]->qty."</td>
						<td rowspan='".$balanceCount."' style='border: 1px solid black;'>".$decodedData[$arrayData]->price."</td>";
				}
				if(count($decodedData[$arrayData]->balance)!=0)
				{
					$middleOne = "<td style='border: 1px solid black;'>".$decodedData[$arrayData]->balance[0]->transactionDate."</td>
							<td style='border: 1px solid black;'>".$decodedData[$arrayData]->balance[0]->qty."</td>
							<td style='border: 1px solid black;'>".$decodedData[$arrayData]->balance[0]->price."</td></tr>";
					
					
					
					$loopPart="";
					for($balanceArrayData=1;$balanceArrayData<count($decodedData[$arrayData]->balance);$balanceArrayData++)
					{
						$loopPart= $loopPart.
							"<tr style='border: 1px solid black;'><td style='border: 1px solid black;'>".$decodedData[$arrayData]->balance[$balanceArrayData]->transactionDate."</td>
							<td style='border: 1px solid black;'>".$decodedData[$arrayData]->balance[$balanceArrayData]->qty."</td>
							<td style='border: 1px solid black;'>".$decodedData[$arrayData]->balance[$balanceArrayData]->price."</td></tr>";
					}
				}
				$lastOne = "</tbody>";
				$finalSettle = $bodyPart.$ifCondition.$middleOne.$loopPart.$lastOne;
				$mainPart = $mainPart.$finalSettle;
		}
		//generate pdf
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		
		$path = $constantArray['stockUrlPdf'];
		$documentPathName = $path.$documentName;
		$htmlBody = $headerPart.$mainPart.$footerPart;
		$mpdf = new mPDF('A4','landscape');
		
		$mpdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold; font-size:20px;">Stock Manage</div>');
		$mpdf->SetDisplayMode('fullpage');
		// error_reporting(E_ALL); 
		// ini_set('display_errors', 1);
		
		//delete older files
		$files = glob($path.'*'); // get all file names
		foreach($files as $file){ // iterate files
		  if(is_file($file))
			unlink($file); // delete file
		}
		
		$mpdf->WriteHTML($htmlBody);
		$mpdf->Output($documentPathName,'F');
		$pathArray = array();
		
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	/**
     * get the specified resource.
     * @param $header-date and product transaction array-data
	 * @return document-path 
     */
	public function generateExcelFile($data)
	{
		$decodedData = json_decode($data);
		$balanceArray = array();
		$balance = array();
		
		$constantClass = new StockManageMpdf();
		$constantArray = $constantClass->constantVariable();
		$decodedData = $decodedData[0];
		
		//calculate stock
		$decodedData = $this->calculateStockManage($decodedData);

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
		$objPHPExcel->getActiveSheet()->setTitle('StockManage');
		
		//heading-start
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,1, 'Stock-Manage');
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:D3');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('E3:G3');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,3, 'Inward/Outward');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,3, 'Balance');
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,4, 'Date');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,4, 'Sale/Purchase');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,4, 'Qty');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,4, 'Amount');
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,4, 'Date');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,4, 'Qty');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,4, 'Amount');
		//heading-end
		
		//logic-start
		$bodyPart="";
		$mainPart = "";
		$middleOne = "";
		$loopPart="";
		$index =0;
		
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			$balanceCount = count($decodedData[$arrayData]->balance);
			$ifCondition="";
			if($balanceCount==0)
			{
				$balanceCount=1;
			}
			
			if(strcmp($decodedData[$arrayData]->transactionType,'Inward')==0)
			{
				$transactionType = "purchase";
			}
			else
			{
				$transactionType = "sales";
			}
			if($arrayData==0)
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+5, $decodedData[0]->transactionDate);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+5, $transactionType);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$arrayData+5, $decodedData[0]->qty);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$arrayData+5, $decodedData[0]->price);
			}
			else
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+5+$index, $decodedData[$arrayData]->transactionDate);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+5+$index, $transactionType);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$arrayData+5+$index,$decodedData[$arrayData]->qty);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(3,$arrayData+5+$index, $decodedData[$arrayData]->price);
			}
			if(count($decodedData[$arrayData]->balance)!=0)
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,$arrayData+5+$index, $decodedData[$arrayData]->balance[0]->transactionDate);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,$arrayData+5+$index, $decodedData[$arrayData]->balance[0]->qty);
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,$arrayData+5+$index, $decodedData[$arrayData]->balance[0]->price);
				
				for($balanceArrayData=1;$balanceArrayData<count($decodedData[$arrayData]->balance);$balanceArrayData++)
				{
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(4,$arrayData+5+$index+1, $decodedData[$arrayData]->balance[$balanceArrayData]->transactionDate);
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(5,$arrayData+5+$index+1, $decodedData[$arrayData]->balance[$balanceArrayData]->qty);
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(6,$arrayData+5+$index+1, $decodedData[$arrayData]->balance[$balanceArrayData]->price);
					$index++;
				}
				
			}
		}
		//logic-end
		
		//CSS
		//Align Center
		$objPHPExcel->getActiveSheet()
		->getStyle('A3')
		->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet()
		->getStyle('E3')
		->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet()
		->getStyle('A1')
		->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
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
		$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($headerStyleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->applyFromArray($headerStyleArray);
		
		// set title style
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyleArray);
		
		// make unique name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx
		$path = $constantArray['stockUrlExcel'];
		$documentPathName = $path.$documentName;
		
		//delete older files
		$files = glob($path.'*'); // get all file names
		foreach($files as $file){ // iterate files
		  if(is_file($file))
			unlink($file); // delete file
		}
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($documentPathName);
		
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
	
	/**
     * get the specified resource.
     * @param product array-data
	 * @return product-data with stock-calcualted-data 
     */
	public function calculateStockManage($decodedData)
	{
		$balanceArray = array();
		$balance = array();
		
		$decodedLength = count($decodedData);
		for($productTrnData = 0;$productTrnData<$decodedLength;$productTrnData++)
		{
			$inward = new stdClass();
			$outward = new stdClass();
			if(strcmp($decodedData[$productTrnData]->transactionType,'Inward')==0)
			{
				if(count($balanceArray)==0)
				{
					$inward->qty = $decodedData[$productTrnData]->qty;
					$inward->price = $decodedData[$productTrnData]->qty * $decodedData[$productTrnData]->price;
					$inward->transactionDate = $decodedData[$productTrnData]->transactionDate;
					array_push($balanceArray,$inward);
				}
				else
				{
					if($balanceArray[0]->qty <0)
					{
					   	$outwardExtra = new stdClass();
						$outwardExtra->qty =0;
						$outwardExtra->transactionDate = $decodedData[$productTrnData]->transactionDate;
						
						$inward->qty = $decodedData[$productTrnData]->qty;
						$inward->price = $decodedData[$productTrnData]->qty * $decodedData[$productTrnData]->price;
						$inward->transactionDate = $decodedData[$productTrnData]->transactionDate;
						
						$balanceLength = count($balanceArray);
						$index=0;
						for($arrayData=0;$arrayData<$balanceLength;$arrayData++)
						{
							$diff =  $inward->qty+$balanceArray[$index]->qty;
							 if($diff==0 || $diff>0)
							 {
								$inward->price = $decodedData[$productTrnData]->price*$diff; 
								$inward->qty = $diff;
								if($arrayData == ($balanceLength-1) && $inward->qty>0)
								{
									$balanceArray[0] = $inward;
								}
								else
								{
									array_splice($balanceArray,$index,1);
								}
							}
							 else if($diff<0)
							 {							
								 $outwardExtra->qty = $balanceArray[$index]->qty + $inward->qty;
								 $outwardExtra->price = 1000;
								 $extra = new stdClass();
								 $extra = clone $outwardExtra;
								 $balanceArray[$index] = $extra;
								 $inward->qty = 0;
								 $index++;
							 }
							 else
							 {
							 }
						}
					}
					else
					{
						$inward->qty = $decodedData[$productTrnData]->qty;
						$inward->price = $decodedData[$productTrnData]->qty * $decodedData[$productTrnData]->price;
						$inward->transactionDate = $decodedData[$productTrnData]->transactionDate;
						array_push($balanceArray,$inward);
					}
				}
			}
			else
			{
				 $outwardExtra = new stdClass();
				 $outwardExtra->qty=0;
				 $outwardExtra->price=$decodedData[$productTrnData]->price;
				 $outwardExtra->transactionDate=$decodedData[$productTrnData]->transactionDate;
				
				 $outward->qty=$decodedData[$productTrnData]->qty;
				 $outward->price=$decodedData[$productTrnData]->price;
				 $outward->transactionDate=$decodedData[$productTrnData]->transactionDate;
				 if(count($balanceArray)==0)
				 {
					 $minusObject = new stdClass();
					 $minusObject->qty=abs($decodedData[$productTrnData]->qty) * -1;
					 $minusObject->price=$decodedData[$productTrnData]->price;
					 $minusObject->transactionDate=$decodedData[$productTrnData]->transactionDate;
					 array_push($balanceArray,$minusObject);
				 }
				 else
				 {
					 if($balanceArray[0]->qty > $outward->qty)
					 {
						 $purchasePrice = $balanceArray[0]->price/$balanceArray[0]->qty;
						 $outward->qty = $balanceArray[0]->qty-$outward->qty;
						 $outward->price = $outward->qty*$purchasePrice;
						 $balanceArray[0] = $outward;
					 } 
					 else if($balanceArray[0]->qty == $outward->qty)
					 {
						 array_splice($balanceArray,0,1);
					 }
					 else if($balanceArray[0]->qty < $outward->qty)
					 {
						 if($balanceArray[0]->qty<0)
						 {
							 $minusObject = new stdClass();
							 $minusObject->qty = abs($outward->qty) * -1;
							 $minusObject->price = $outward->price;
							 $minusObject->transactionDate = $outward->transactionDate;
							 array_push($balanceArray,$minusObject);
						 }
						 else
						 {
							 $index=0;
							 $countBalanceArray = count($balanceArray);
							 for($balanceArrayData=0;$balanceArrayData<$countBalanceArray;$balanceArrayData++)
							 {
								 $diff = $outward->qty - $balanceArray[$index]->qty;
								 if($diff==0 || $diff>0)
								 {
									 $outward->qty = $diff;
									 if($balanceArrayData == ($countBalanceArray-1) && $outward->qty > 0)
									 {
										 $outwardExtra->qty = abs($outward->qty) * -1;
										 $balanceArray[0] = $outwardExtra;
									 }
									 else
									 {
										 array_splice($balanceArray,$index,1);
									 }
								}
								 else if($diff<0)
								 {							
									 $purchasePrice = $balanceArray[$index]->price/$balanceArray[$index]->qty;
									 $outwardExtra->qty = $balanceArray[$index]->qty - $outward->qty;
									 $outwardExtra->price = $outwardExtra->qty * $purchasePrice;
									 $extra = new stdClass();
									 $extra = clone $outwardExtra;
									 $balanceArray[$index] = $extra;
									 $outward->qty = 0;
									 $index++;
								 }
								 else
								 {
								 }
							 }
						 }	
					 }
					 else
					 {
					 }
				 }		
			}
			$balance[$productTrnData] = array_slice($balanceArray,0);
			$decodedData[$productTrnData]->balance = $balance[$productTrnData];
		}
		return $decodedData;
	}
}