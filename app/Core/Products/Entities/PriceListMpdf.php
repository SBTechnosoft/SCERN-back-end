<?php
namespace ERP\Core\Products\Entities;

use mPDF;
use ERP\Entities\Constants\ConstantClass;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Alignment;
use ERP\Core\Settings\Services\SettingService;
use ERP\Core\ProductCategories\Services\ProductCategoryService;
use ERP\Core\ProductGroups\Services\ProductGroupService;
use ERP\Core\Companies\Services\CompanyService;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PriceListMpdf extends ConstantClass
{
	/**
     * get the specified resource.
     * @param $header-date and product transaction array-data
	 * @return document-path 
     */
	public function generatePdf($headerData,$data)
	{
		$setting_color = false;
		$setting_size = false;

		$settingService= new SettingService();
		$settingData = $settingService->getData();
		$settingData = json_decode($settingData);

		$stCount = count($settingData);
		$stIndex = 0;
		while ($stIndex < $stCount) {
			$settingSingleData = $settingData[$stIndex];

			if($settingSingleData->settingType == 'product'){
				if ($settingSingleData->productColorStatus == 'enable') {
					$setting_color = true;
				}
				if ($settingSingleData->productSizeStatus == 'enable') {
					$setting_size = true;
				}
				break;
			}
			$stIndex++;
		}

		$decodedData = json_decode($data);
		// print_r($decodedData);
		// exit;
		//generate pdf
		$constantClass = new PriceListMpdf();
		$constantArray = $constantClass->constantVariable();
		$headerPart = "<table style='border: 1px solid black; width:100%'>
						<thead style='border: 1px solid black;'>
							<tr style='border: 1px solid black;'>
								<th style='border: 1px solid black;'>product-Category</th>
								<th style='border: 1px solid black;'>product-Group</th>
								<th style='border: 1px solid black;'>product-Name</th>";
		if($setting_color){
			$headerPart	.= "<th style='border: 1px solid black;'>Color</th>";
		}
		
		if($setting_size){
			$headerPart .= "<th style='border: 1px solid black;'>Size</th>";
		}

		$headerPart .= 	"<th style='border: 1px solid black;'>Price</th>
								<th style='border: 1px solid black;'>MRP</th>
								<th style='border: 1px solid black;'>Quantity</th>
							</tr>
						</thead><tbody>";
		$bodyPart = "";
		
		$CompanyService = new CompanyService();
		$companyData = '';

		$ProductCategoryService = new ProductCategoryService();
		$ProductGroupService = new ProductGroupService();

		$productCatId = array();
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			if ($companyData == ''){
				$companyData = json_decode($CompanyService->getCompanyData($decodedData[$arrayData]->companyId));
			}


			$categoryData = json_decode($ProductCategoryService->getProductCatData($decodedData[$arrayData]->productCategoryId));
			$groupData = json_decode($ProductGroupService->getProductGrpData($decodedData[$arrayData]->productGroupId));
			
			if(strcmp($headerData['salestype'][0],'retail_sales')==0)
			{
				if($decodedData[$arrayData]->purchasePrice==0 || $decodedData[$arrayData]->purchasePrice=="")
				{
					$decodedData[$arrayData]->purchasePrice = $decodedData[$arrayData]->mrp;
				}
				$margin[$arrayData] = ($decodedData[$arrayData]->margin/100)*$decodedData[$arrayData]->purchasePrice;
				$margin[$arrayData] = $margin[$arrayData]+$decodedData[$arrayData]->marginFlat;
				$decodedData[$arrayData]->purchasePrice = $decodedData[$arrayData]->purchasePrice +$margin[$arrayData];
				// $decodedData[$arrayData]->vat = ($decodedData[$arrayData]->vat/100)*$decodedData[$arrayData]->purchasePrice;
				// $totalAmount[$arrayData] = $decodedData[$arrayData]->purchasePrice+$decodedData[$arrayData]->vat;
				// $additionalTax[$arrayData] = ($decodedData[$arrayData]->additionalTax/100)*$decodedData[$arrayData]->purchasePrice;
			}	
			else
			{
				$wholeSaleMargin[$arrayData] = ($decodedData[$arrayData]->wholesaleMargin/100)*$decodedData[$arrayData]->purchasePrice;
				$wholeSaleMargin[$arrayData] = $wholeSaleMargin[$arrayData]+$decodedData[$arrayData]->wholesaleMarginFlat;
				$decodedData[$arrayData]->purchasePrice = $decodedData[$arrayData]->purchasePrice +$wholeSaleMargin[$arrayData];
				// $decodedData[$arrayData]->vat = ($decodedData[$arrayData]->vat/100)*$decodedData[$arrayData]->purchasePrice;;
				// $totalAmount[$arrayData] = $decodedData[$arrayData]->purchasePrice+$decodedData[$arrayData]->vat;
				// $additionalTax[$arrayData] = ($decodedData[$arrayData]->additionalTax/100)*$decodedData[$arrayData]->purchasePrice;
			}
			
			//convert amount(round) into their company's selected decimal points
			$decodedData[$arrayData]->purchasePrice = round($decodedData[$arrayData]->purchasePrice,$companyData->noOfDecimalPoints);
			$decodedData[$arrayData]->mrp = round($decodedData[$arrayData]->mrp,$companyData->noOfDecimalPoints);
			// $decodedData[$arrayData]->vat = round($decodedData[$arrayData]->vat,$companyData->noOfDecimalPoints);
			// $totalAmount[$arrayData] = round($totalAmount[$arrayData],$companyData->noOfDecimalPoints);
			// $additionalTax[$arrayData] = round($additionalTax[$arrayData],$companyData->noOfDecimalPoints);
			
			$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>";
			if($arrayData!=0)
			{
				$productCatId[$arrayData] = $categoryData->productCategoryId;
				if($productCatId[$arrayData]!=$productCatId[$arrayData-1])
				{
					$bodyPart = $bodyPart."<td style='border: 1px solid black;'>".$categoryData->productCategoryName."</td>";
				}
				else
				{
					$bodyPart = $bodyPart."<td style='border: 1px solid black;'></td>";
				}
			}
			else
			{
				$bodyPart = $bodyPart."<td style='border: 1px solid black;'>".$categoryData->productCategoryName."</td>";
				$productCatId[$arrayData] = $categoryData->productCategoryId;
			}

			/* Center part */
			$tempCenterPart = "<td style='border: 1px solid black;'>".$groupData->productGroupName."</td>
									<td style='border: 1px solid black;'>".$decodedData[$arrayData]->productName."</td>";

			if($setting_color){
				$tempCenterPart .= "<td style='border: 1px solid black;'>".$decodedData[$arrayData]->color."</td>";
			}

			if($setting_size){
				$tempCenterPart .= "<td style='border: 1px solid black;'>".$decodedData[$arrayData]->size."</td>";
			}
									
									
			$tempCenterPart .= "<td style='border: 1px solid black;'>".$decodedData[$arrayData]->purchasePrice."</td>
								<td style='border: 1px solid black;'>".$decodedData[$arrayData]->mrp."</td>
								<td style='border: 1px solid black;'>".$decodedData[$arrayData]->quantity."</td></tr>";
			/* End */

			$bodyPart = $bodyPart.$tempCenterPart;
			
		}
		$footerPart = "</tbody></table>";
		$htmlBody = $headerPart.$bodyPart.$footerPart;
		
		//make unique name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		
		$path = $constantArray['priceListPdf'];
		$documentPathName = $path.$documentName;
		$mpdf = new mPDF('A4','landscape');
		$mpdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold; font-size:20px;">Price List</div>');
		$mpdf->SetDisplayMode('fullpage');
		
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
	public function generateExcelFile($headerData,$data)
	{
		$setting_color = false;
		$setting_size = false;

		$settingService= new SettingService();
		$settingData = $settingService->getData();
		$settingData = json_decode($settingData);

		$stCount = count($settingData);
		$stIndex = 0;
		while ($stIndex < $stCount) {
			$settingSingleData = $settingData[$stIndex];

			if($settingSingleData->settingType == 'product'){
				if ($settingSingleData->productColorStatus == 'enable') {
					$setting_color = true;
				}
				if ($settingSingleData->productSizeStatus == 'enable') {
					$setting_size = true;
				}
			}
			$stIndex++;
		}

		$constantClass = new PriceListMpdf();
		$constantArray = $constantClass->constantVariable();
		
		$decodedData = json_decode($data);
		
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
		$objPHPExcel->getActiveSheet()->setTitle('PriceList');
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,1, 'Price-List');
		
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,3, 'Category-Name');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,3, 'Group-Name');
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,3, 'Product-Name');

		$tempExcelIndex = 2;

		if($setting_color){
			$tempExcelIndex++;
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,3, 'Color');
		}

		if($setting_size){
			$tempExcelIndex++;
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,3, 'Size');
		}
		
		$tempExcelIndex++;
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,3, 'Purchase-Price');
		$tempExcelIndex++;
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,3, 'MRP');
		$tempExcelIndex++;
		$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,3, 'Quantity');
		
		$productCatId = array();
		
		$CompanyService = new CompanyService();
		$companyData = '';

		$ProductCategoryService = new ProductCategoryService();
		$ProductGroupService = new ProductGroupService();


		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
   		{        
   			if ($companyData == ''){
				$companyData = json_decode($CompanyService->getCompanyData($decodedData[$arrayData]->companyId));
			}


			$categoryData = json_decode($ProductCategoryService->getProductCatData($decodedData[$arrayData]->productCategoryId));
			$groupData = json_decode($ProductGroupService->getProductGrpData($decodedData[$arrayData]->productGroupId));
			
			if(strcmp($headerData['salestype'][0],'retail_sales')==0)
			{
				if($decodedData[$arrayData]->purchasePrice==0 || $decodedData[$arrayData]->purchasePrice=="")
				{
					$decodedData[$arrayData]->purchasePrice = $decodedData[$arrayData]->mrp;
				}
				$margin[$arrayData] = ($decodedData[$arrayData]->margin/100)*$decodedData[$arrayData]->purchasePrice;
				$margin[$arrayData] = $margin[$arrayData]+$decodedData[$arrayData]->marginFlat;
				$decodedData[$arrayData]->purchasePrice = $decodedData[$arrayData]->purchasePrice +$margin[$arrayData];
				// $decodedData[$arrayData]->vat = ($decodedData[$arrayData]->vat/100)*$decodedData[$arrayData]->purchasePrice;
				// $totalAmount[$arrayData] = $decodedData[$arrayData]->purchasePrice+$decodedData[$arrayData]->vat;
				// $additionalTax[$arrayData] = ($decodedData[$arrayData]->additionalTax/100)*$decodedData[$arrayData]->purchasePrice;
			}	
			else
			{
				$wholeSaleMargin[$arrayData] = ($decodedData[$arrayData]->wholesaleMargin/100)*$decodedData[$arrayData]->purchasePrice;
				$wholeSaleMargin[$arrayData] = $wholeSaleMargin[$arrayData]+$decodedData[$arrayData]->wholesaleMarginFlat;
				$decodedData[$arrayData]->purchasePrice = $decodedData[$arrayData]->purchasePrice +$wholeSaleMargin[$arrayData];
				// $decodedData[$arrayData]->vat = ($decodedData[$arrayData]->vat/100)*$decodedData[$arrayData]->purchasePrice;;
				// $totalAmount[$arrayData] = $decodedData[$arrayData]->purchasePrice+$decodedData[$arrayData]->vat;
				// $additionalTax[$arrayData] = ($decodedData[$arrayData]->additionalTax/100)*$decodedData[$arrayData]->purchasePrice;
			}
			
			// convert amount(round) into their company's selected decimal points
			$decodedData[$arrayData]->purchasePrice = round($decodedData[$arrayData]->purchasePrice,$companyData->noOfDecimalPoints);
			$decodedData[$arrayData]->mrp = round($decodedData[$arrayData]->mrp,$companyData->noOfDecimalPoints);
			// $decodedData[$arrayData]->vat = round($decodedData[$arrayData]->vat,$companyData->noOfDecimalPoints);
			// $totalAmount[$arrayData] = round($totalAmount[$arrayData],$companyData->noOfDecimalPoints);
			// $additionalTax[$arrayData] = round($additionalTax[$arrayData],$companyData->noOfDecimalPoints);
			
			if($arrayData!=0)
			{
				$productCatId[$arrayData] = $categoryData->productCategoryId;
				if($productCatId[$arrayData]!=$productCatId[$arrayData-1])
				{
					$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+4, $categoryData->productCategoryName);
				}
			}
			else
			{
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,$arrayData+4, $categoryData->productCategoryName);
				$productCatId[$arrayData] = $categoryData->productCategoryId;
			}
			
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,$arrayData+4, $groupData->productGroupName);
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,$arrayData+4, $decodedData[$arrayData]->productName);

			$tempExcelIndex = 2;

			if($setting_color){
				$tempExcelIndex++;
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,$arrayData+4, $decodedData[$arrayData]->color);
			}

			if($setting_size){
				$tempExcelIndex++;
				$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,$arrayData+4, $decodedData[$arrayData]->size);
			}

			$tempExcelIndex++;
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,$arrayData+4, $decodedData[$arrayData]->purchasePrice);
			$tempExcelIndex++;
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,$arrayData+4, $decodedData[$arrayData]->mrp);
			$tempExcelIndex++;
			$objPHPExcel->setActiveSheetIndex()->setCellValueByColumnAndRow($tempExcelIndex,$arrayData+4, $decodedData[$arrayData]->quantity);
		}
		
		// style for header
		$headerStyleArray = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'black'),
			'size'  => 10,
			'name'  => 'Verdana'
		));
		
		// style for Title
		$titleStyleArray = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'black'),
			'size'  => 15,
			'name'  => 'Verdana'
		));
		
		// set header style
		$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($headerStyleArray);
		// $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($headerStyleArray);
		// $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($headerStyleArray);
		// $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($headerStyleArray);
		// $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($headerStyleArray);
		// $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($headerStyleArray);
		// $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($headerStyleArray);
		
		// set title style
		$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($titleStyleArray);
		
		// make unique name
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".xls"; //xslx
		$path = $constantArray['priceListExcel'];
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
}