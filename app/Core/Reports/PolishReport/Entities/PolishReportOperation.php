<?php
namespace ERP\Core\Reports\PolishReport\Entities;

use mPDF;
use ERP\Entities\Constants\ConstantClass;
use stdclass;
use ERP\Model\Products\ProductModel;
use ERP\Exceptions\ExceptionMessage;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PolishReportOperation extends ProductModel
{
	/**
	 * set data into pdf file
	 * $param database decoded-data
	 * @return the array-result
	*/
	public function generatePdf($data,$fromDate,$toDate)
	{
		$decodedData = json_decode($data);
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$tableData="";
		$heading = 	'<div style=" font-weight: bold; font-size:20px;text-align:center;">'.$decodedData[0]->company->companyName.'</div>
					<div style=" font-weight: bold; font-size:12px;text-align:center;">'.$decodedData[0]->company->address1.$decodedData[0]->company->address2.'</div>
					<div style=" font-weight: bold; font-size:12px;text-align:center;">SALES DETAIL REPORT</div>
					<div style="font-weight: bold; font-size:12px;text-align:center;">Date: '.$fromDate.' To: '.$toDate.'</div>';
		
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			$headerPart = "<table style='width:100%;border:1;border-collapse: collapse;'>
						<tbody>";
			$bodyPart = "";
			
			$decodedProductArray = json_decode($decodedData[$arrayData]->productArray);
			$productCount = $decodedProductArray->inventory;
			$countOfRaw = count($productCount)+5;
			$flag=0;
			$fileData[$arrayData] = $decodedData[$arrayData]->file;
			
			for($documentArray=0;$documentArray<count($fileData[$arrayData]);$documentArray++)
			{
				if(strcmp($fileData[$arrayData][$documentArray]->documentFormat,'pdf')!=0 && strcmp($fileData[$arrayData][$documentArray]->documentFormat,'')!=0)
				{
					$flag=1;
					$documentUrl = $fileData[$arrayData][$documentArray]->documentUrl. $fileData[$arrayData][$documentArray]->documentName;
					$bodyPart = $bodyPart."	<tr>
								<td rowspan='".$countOfRaw."' style='width:50%;'><img src=".$documentUrl." width='340px' height='200px' alt='img'/></td>";
					break;
				}
			}
			if($flag==0)
			{
				$bodyPart = $bodyPart."<tr><td rowspan='".$countOfRaw."' style='width:50%;padding:20px;'><img src=".$constantArray['noImage']." width='200' height='200' alt='img'/></td>";
			}
			$bodyPart = $bodyPart. "<td style='width:30%;text-align:center;border-left: 1px solid black;'>Invoice No: ".$decodedData[$arrayData]->invoiceNumber."</td>
									<td></td>
									<td style='width:25%;text-align:center;'>Date: ".$decodedData[$arrayData]->entryDate."</td></tr>
									<tr><td style='width:25%;text-align:center;border-left: 1px solid black;'>Name: ".$decodedData[$arrayData]->client->clientName."</td></tr>
									<tr><td style='width:25%;text-align:center; border-left: 1px solid black;'>Address: ".$decodedData[$arrayData]->client->address1."</td></tr>
									<tr><td style='width:25%;text-align:center; border-bottom: 1px solid black; border-left: 1px solid black;'>Mobile No: ".$decodedData[$arrayData]->client->contactNo."</td></tr>
									
									<tr>
										<td style='width:16%;text-align:center;border-left: 1px solid black;'>Description</td>
										<td style='width:16%;text-align:center;border-top: 1px solid black;'>Qty</td>
										<td style='width:16%;text-align:center;border-top: 1px solid black;'>Frame No</td>
									</tr>";
			for($productArray=0;$productArray<count($productCount);$productArray++)
			{
				$productModel = new PolishReportOperation();
				$productResult = $productModel->getData($productCount[$productArray]->productId);
				$decodedProductResult[$productArray] = json_decode($productResult);
				
				if(strcmp($productResult,$exceptionArray['404'])!=0)
				{
					$bodyPart=$bodyPart."<tr>
											<td style='width:16%;text-align:center;border-left: 1px solid black;'>".$decodedProductResult[$productArray][0]->product_name."</td>
											<td style='width:16%;text-align:center;'>".$productCount[$productArray]->qty."</td>
											<td style='width:16%;text-align:center;'>".$productCount[$productArray]->frameNo."</td>
										</tr>";
				}
			}
			$footerPart = "</tbody></table>";
			$tableData = $tableData.$headerPart.$bodyPart.$footerPart;
		}
		// ini_set('memory_limit', '256M');
		// ini_set('upload_max_filesize', '256M');
		// ini_set('post_max_size', '256M');
		$htmlBody="";
		$htmlBody = $heading.$tableData;
		
		// generate pdf
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999)."_PolishReport.pdf";
		$path = $constantArray['polishReportUrl'];
		$documentPathName = $path.$documentName;
		$mpdf = new mPDF('A4','landscape');
		$mpdf->SetDisplayMode('fullpage');
		ini_set('memory_limit', '1024M');
		// echo $htmlBody;
		$mpdf->WriteHTML($htmlBody);
		// ini_set('memory_limit', '256M');
		
		$mpdf->Output($documentPathName,'F');
		
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		
		return $pathArray;
	}
}