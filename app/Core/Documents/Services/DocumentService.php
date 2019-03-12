<?php
namespace ERP\Core\Documents\Services;

use ERP\Model\Accounting\Bills\BillModel;
use ERP\Core\Settings\Templates\Entities\TemplateTypeEnum;
use ERP\Core\Settings\Templates\Services\TemplateService;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Documents\Entities\DocumentMpdf;
use ERP\Core\Accounting\Bills\Entities\EncodeData;
use ERP\Model\Documents\DocumentModel;
use ERP\Entities\Constants\ConstantClass;
use mPDF;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class DocumentService extends BillModel
{
    /**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function insert()
	{
        //get the data from persistable object 
		$documentArray = func_get_arg(0);
		for($filaArray=0;$filaArray<count($documentArray);$filaArray++)
		{
			 $simpleArray[$filaArray] = array();
			 $simpleArray[$filaArray][0] = $documentArray[$filaArray]->getDocumentName();
			 $simpleArray[$filaArray][1] = $documentArray[$filaArray]->getDocumentSize();
			 $simpleArray[$filaArray][2] = $documentArray[$filaArray]->getDocumentFormat();
			 $simpleArray[$filaArray][3] = $documentArray[$filaArray]->getDocumentUrl();
		}
		 return $simpleArray;
	}
	 
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getSaleData($saleId,$headerData)
	{
		$billModel = new BillModel();
		if(array_key_exists("issalesorder",$headerData))
		{
			$saleData = $billModel->getSaleOrderIdData($saleId);
		}
		else
		{
			$saleData = $billModel->getSaleIdData($saleId);
		}

		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($saleData,$exceptionArray['404'])==0)
		{
			return $saleData;
		}
		else
		{
			$encoded = new EncodeData();
			$encodeData = $encoded->getEncodedData($saleData);
			$decodedSaleData = json_decode($encodeData);
			
			$templateType = new TemplateTypeEnum();
			$templateArray = $templateType->enumArrays();
			if(strcmp($_SERVER['REQUEST_URI'],"/accounting/bills/".$saleId."/payment")==0)
			{
				$templateType = $templateArray['paymentTemplate'];
			}
			else
			{
				$templateType = $templateArray['invoiceTemplate'];
			}
			$emailTemplateType = $templateArray['emailNewOrderTemplate'];
			$blankTemplateType = $templateArray['blankTemplate'];
			$smsTemplateType = $templateArray['smsNewOrderTemplate'];
			
			$templateService = new TemplateService();
			$templateData = $templateService->getSpecificData($decodedSaleData->company->companyId,$templateType);
			$templateData = $templateService->joinProductHeadWithTemplate($templateData);
			$emailTemplateData = $templateService->getSpecificData($decodedSaleData->company->companyId,$emailTemplateType);
			$blankTemplateData = $templateService->getSpecificData($decodedSaleData->company->companyId,$blankTemplateType);
			$smsTemplateData = $templateService->getSpecificData($decodedSaleData->company->companyId,$smsTemplateType);
			if(strcmp($templateData,$exceptionArray['404'])==0)
			{
				return $templateData;
			}
			else
			{
				$documentMpdf = new DocumentMpdf();
				if(strcmp($_SERVER['REQUEST_URI'],"/accounting/bills/".$saleId."/payment")==0)
				{
					$documentMpdf = $documentMpdf->mpdfPaymentGenerate($templateData,$encodeData,$emailTemplateData,$blankTemplateData,$smsTemplateData);
					return $documentMpdf;
				}
				else
				{
					$documentMpdf = $documentMpdf->mpdfGenerate($templateData,$encodeData,$headerData,$emailTemplateData,$blankTemplateData,$smsTemplateData);
					return $documentMpdf;
				}
			}
		}
	}
	
	/**
     * get all Data of SaleIds and Merge PDF in One & then return 
     * @return status
     */
	public function getDocbulkPrintData($saleIds)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$documentModel = new DocumentModel();
		$saleData = $documentModel->getSaleIdsDocuments($saleIds);
		
		if(!is_array($saleData)){
			return $saleData;
		}

		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();

		$filenames = array();
		$i=0;
		$cnt = count($saleData);
		while($i < $cnt){
			$filenames[] = $constantArray['billUrl'].$saleData[$i]->document_name;
			$i++;
		}

		if(empty($filenames)){
			return $exceptionArray['404'];
		}

		/* New PDF Name */
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		$newFileName = $constantArray['multipleBillUrl'].$documentName;
		/* New PDF Name */
		if (!file_exists($constantArray['multipleBillUrl'])) {
			mkdir($constantArray['multipleBillUrl'], 0777, true);
		}
		// $this->deleteFiles($constantArray['multipleBillUrl']);
		
		$returnData = $this->mergePDFFiles($filenames,$newFileName);

		
		if(!$returnData)
		{
			return $exceptionArray['404'];
		}
		$path = array();
		$path['documentPath'] = $newFileName;
		return json_encode($path);

	}

	public function mergePDFFiles(Array $filenames, $outFile) 
	{
	    $mpdf = new mPDF();
	   if ($filenames) {

	        $filesTotal = sizeof($filenames);
	        $fileNumber = 1;

	        $mpdf->SetImportUse();

	        if (!file_exists($outFile)) {
	            $handle = fopen($outFile, 'w');
	            fclose($handle);
	        }

	        foreach ($filenames as $fileName) {
	            if (file_exists($fileName)) {
	                $pagesInFile = $mpdf->SetSourceFile($fileName);
	                for ($i = 1; $i <= $pagesInFile; $i++) {
	                    $tplId = $mpdf->ImportPage($i);
	                    $mpdf->UseTemplate($tplId);
	                    if (($fileNumber < $filesTotal) || ($i != $pagesInFile)) {
	                        $mpdf->WriteHTML('<pagebreak />');
	                    }
	                }
	            }
	            $fileNumber++;
	        }

	        $mpdf->Output($outFile);
	        return true;
	    }
	    return false;
	}

	public function deleteFiles($dirPath)
	{
		if ($dirPath != ''){
			if (!file_exists($dirPath)) {
				mkdir($dirPath, 0777, true);
			} else {
				$files = glob($dirPath.'*');

				//Loop through the file list.
				foreach($files as $file){
				    //Make sure that this is a file and not a directory.
				    if(is_file($file)){
				        //Use the unlink function to delete the file.
				        unlink($file);
				    }
				}
			}
		} 
		
	}
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getQuotationData($quotationBillId,$companyId,$quotationData,$headerData)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$templateType = new TemplateTypeEnum();
		$templateArray = $templateType->enumArrays();
		$templateType = $templateArray['quotationTemplate'];
		$emailTemplateType = $templateArray['emailNewOrderTemplate'];
		$blankTemplateType = $templateArray['blankTemplate'];
		$smsTemplateType = $templateArray['smsNewOrderTemplate'];

		$templateService = new TemplateService();
		$templateData = $templateService->getSpecificData($companyId,$templateType);
		$emailTemplateData = $templateService->getSpecificData($companyId,$emailTemplateType);

		$blankTemplateData = $templateService->getSpecificData($companyId,$blankTemplateType);
		$smsTemplateData = $templateService->getSpecificData($companyId,$smsTemplateType);

		if(strcmp($templateData,$exceptionArray['404'])==0)
		{
			return $templateData;
		}
		else
		{
			$headerArray = $headerData;
			$documentMpdf = new DocumentMpdf();
			$documentMpdf = $documentMpdf->quotationMpdfGenerate($templateData,$quotationData,$headerArray,$emailTemplateData,$blankTemplateData);
			return $documentMpdf;
		}
		
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getJobformData($inputData)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$templateType = new TemplateTypeEnum();
		$templateArray = $templateType->enumArrays();
		$templateType = $templateArray['jobCardTemplate'];
		$templateService = new TemplateService();
		$companyId = $inputData[0]->company->companyId;
		$templateData = $templateService->getSpecificData($companyId,$templateType);
		if(strcmp($templateData,$exceptionArray['404'])==0)
		{
			return $templateData;
		}
		else
		{
			$documentMpdf = new DocumentMpdf();
			$documentMpdf = $documentMpdf->jobFormMpdfGenerate($templateData,$inputData);
			return $documentMpdf;
		}
		
	}
}