<?php
namespace ERP\Core\Accounting\TrialBalance\Entities;

use mPDF;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Companies\Services\CompanyService;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TrialBalanceMpdf extends CompanyService
{
	public function generatePdf($data)
	{
		$decodedData = json_decode($data);
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$headerPart = "<table style='border: 1px solid black; width:100%'>
						<thead style='border: 1px solid black; width:100%;'>
							<tr style='border: 1px solid black;width:100%;'>
								<th style='border: 1px solid black; width:50%;'>Particular</th>
								<th style='border: 1px solid black;width:25%;'>Debit</th>
								<th style='border: 1px solid black;width:25%;'>Credit</th>
							</tr>
						</thead><tbody>";
		$bodyPart = "";
		$creditAmountTotal = 0;
		$debitAmountTotal = 0;
		$trialBalance = new TrialBalanceMpdf();
		$companyDetail = $trialBalance->getCompanyData($decodedData[0]->ledger->companyId);
		$decodedCompanyData = json_decode($companyDetail);
		
		for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
		{
			$decodedData[$arrayData]->amount = round($decodedData[$arrayData]->amount,$decodedCompanyData->noOfDecimalPoints);
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
			if($arrayData == count($decodedData)-1)
			{
				$debitAmountTotal = round($debitAmountTotal,$decodedCompanyData->noOfDecimalPoints);
				$creditAmountTotal = round($creditAmountTotal,$decodedCompanyData->noOfDecimalPoints);
				$bodyPart = $bodyPart."	<tr style='border: 1px solid black;'>
									<td style='border: 1px solid black; width:50%;'>Total</td>
									<td style='border: 1px solid black; width:25%;text-align:center;'>".$debitAmountTotal."</td>
									<td style='border: 1px solid black;width:25%; text-align:center;'>".$creditAmountTotal."</td></tr>";
			}
		}
		$footerPart = "</tbody></table>";
		$htmlBody = $headerPart.$bodyPart.$footerPart;
		
		//generate pdf
		$dateTime = date("d-m-Y h-i-s");
		$convertedDateTime = str_replace(" ","-",$dateTime);
		$splitDateTime = explode("-",$convertedDateTime);
		$combineDateTime = $splitDateTime[0].$splitDateTime[1].$splitDateTime[2].$splitDateTime[3].$splitDateTime[4].$splitDateTime[5];
		$documentName = $combineDateTime.mt_rand(1,9999).mt_rand(1,9999).".pdf";
		
		$path = $constantArray['trialBalanceUrl'];
		$documentPathName = $path.$documentName;
		$mpdf = new mPDF('A4','landscape');
		
		$mpdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold; font-size:20px;">Trial Balance</div>');
		
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->WriteHTML($htmlBody);
		$mpdf->Output($documentPathName,'F');
		$pathArray = array();
		$pathArray['documentPath'] = $documentPathName;
		return $pathArray;
	}
}