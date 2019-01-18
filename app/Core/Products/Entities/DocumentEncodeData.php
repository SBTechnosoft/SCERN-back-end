<?php
namespace ERP\Core\Products\Entities;

// use ERP\Core\Products\Entities\Product;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
/**
 *
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class DocumentEncodeData 
{
    public function getEncodedData($status)
	{
		$constantArray = new ConstantClass();
		$constantArrayData = $constantArray->constantVariable();

		$documentPath = $constantArrayData['productBarcode'];
		$decodedJson = json_decode($status,true);
		$dataCount = count($decodedJson);
		$documentDataArray = array();

		for($decodedData=0;$decodedData<$dataCount;$decodedData++)
		{
			$documentDataArray[$decodedData]['documentId'] = $decodedJson[$decodedData]['document_id'];
			$documentDataArray[$decodedData]['documentName'] = $decodedJson[$decodedData]['document_name'];
			$documentDataArray[$decodedData]['documentSize'] = $decodedJson[$decodedData]['document_size'];
			$documentDataArray[$decodedData]['documentFormat'] = $decodedJson[$decodedData]['document_format'];
			$documentDataArray[$decodedData]['documentType'] = $decodedJson[$decodedData]['document_type'];
			$documentDataArray[$decodedData]['productId'] = $decodedJson[$decodedData]['product_id'];
			$documentDataArray[$decodedData]['documentPath'] = 
			strcmp($decodedJson[$decodedData]['document_type'],'CoverImage')==0 ? $constantArrayData['productCoverDocumentUrl'] : $constantArrayData['productDocumentUrl'];

			$documentDataArray[$decodedData]['createdAt'] = 
			$decodedJson[$decodedData]['created_at'] == "0000-00-00 00:00:00" ? "0000-00-00" : Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$decodedJson[$decodedData]['created_at'])->format('d-m-Y');

			$documentDataArray[$decodedData]['updatedAt'] = 
			$decodedJson[$decodedData]['updated_at'] == "0000-00-00 00:00:00" ? "0000-00-00" : Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$decodedJson[$decodedData]['updated_at'])->format('d-m-Y');
		}

		return json_encode($documentDataArray);
	}
}