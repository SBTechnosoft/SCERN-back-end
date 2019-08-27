<?php
namespace ERP\Api\V1_0\Accounting\PurchaseBills\Transformers;

use Carbon;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class PurchaseInventoryTransformer
{
	/**
	 * @param Request Object
	 * @return array/error message
	 */
	public function trimInventory($inventoryJson, $purchaseId)
	{
		$inventory = json_decode($inventoryJson, true);
		$inventory = $inventory['inventory'];
		$response = array();
		foreach ($inventory as $inv_record) {
			// $inv_record;
			$single = array();
			$single['purchase_id'] = trim($purchaseId);
			$single['product_id'] = trim($inv_record['productId']);
			$single['product_name'] = trim($inv_record['productName']);
			$single['color'] = array_key_exists('color', $inv_record) ? trim($inv_record['color']) : '';
			$single['size'] = array_key_exists('size', $inv_record) ? trim($inv_record['size']) : '';
			$single['variant'] = array_key_exists('variant', $inv_record) ? trim($inv_record['variant']) : '';
			$single['frame_no'] = array_key_exists('frameNo', $inv_record) ? trim($inv_record['frameNo']) : '';
			$single['measurement_unit'] = array_key_exists('measurementUnit', $inv_record) ? trim($inv_record['measurementUnit']) : '';
			$single['qty'] = is_numeric($inv_record['qty']) ? floatval(trim($inv_record['qty'])) : 1;
			$single['cgst_percentage'] = is_numeric($inv_record['cgstPercentage']) ? floatval(trim($inv_record['cgstPercentage'])) : 0;
			$single['cgst_amount'] = is_numeric($inv_record['cgstAmount']) ? floatval(trim($inv_record['cgstAmount'])) : 0;
			$single['sgst_percentage'] = is_numeric($inv_record['sgstPercentage']) ? floatval(trim($inv_record['sgstPercentage'])) : 0;
			$single['sgst_amount'] = is_numeric($inv_record['sgstAmount']) ? floatval(trim($inv_record['sgstAmount'])) : 0;
			$single['igst_percentage'] = is_numeric($inv_record['igstPercentage']) ? floatval(trim($inv_record['igstPercentage'])) : 0;
			$single['igst_amount'] = is_numeric($inv_record['igstAmount']) ? floatval(trim($inv_record['igstAmount'])) : 0;
			$single['cess_percentage'] = array_key_exists('cessPercentage', $inv_record) && is_numeric($inv_record['cessPercentage']) ? floatval(trim($inv_record['cessPercentage'])) : 0;
			$single['cess_flat'] = array_key_exists('cessFlat', $inv_record) && is_numeric($inv_record['cessFlat']) ? floatval(trim($inv_record['cessFlat'])) : 0;
			$single['cess_amount'] = array_key_exists('cessAmount', $inv_record) && is_numeric($inv_record['cessAmount']) ? floatval(trim($inv_record['cessAmount'])) : 0;
			$single['price'] = is_numeric($inv_record['price']) ? floatval(trim($inv_record['price'])) : 0;
			$single['discount'] = is_numeric($inv_record['discount']) ? floatval(trim($inv_record['discount'])) : 0;

			$single['discount_type'] = array_key_exists('discountType', $inv_record) && $inv_record['discountType'] == 'percentage' ? 'percentage' : 'flat';

			$single['amount'] = is_numeric($inv_record['amount']) ? floatval(trim($inv_record['amount'])) : 0;
			$single['created_at'] = Carbon\Carbon::now();
			
			$single['stock_ft'] = $single['qty'];
			$single['total_ft'] = $single['qty'];

			$single['real_qty_data'] = $single['qty'];

			$single['length_value'] = 1;
			$single['width_value'] = 1;
			$single['height_value'] = 1;
			$single['devide_factor'] = 1;

			if(array_key_exists('stockFt', $inv_record) && $inv_record['stockFt'] != '' && is_numeric($inv_record['stockFt'])) {
				$single['stock_ft'] = floatval($inv_record['stockFt']) ? : $single['qty'];
			}
			if(array_key_exists('totalFt', $inv_record) && $inv_record['totalFt'] != '' && is_numeric($inv_record['totalFt'])) {
				$single['total_ft'] = floatval($inv_record['totalFt']) ? : $single['qty'];
			}
			if(array_key_exists('lengthValue', $inv_record) && $inv_record['lengthValue'] != '' && is_numeric($inv_record['lengthValue'])) {
				$single['length_value'] = floatval($inv_record['lengthValue']) ? : 1;
			}
			if(array_key_exists('widthValue', $inv_record) && $inv_record['widthValue'] != '' && is_numeric($inv_record['widthValue'])) {
				$single['width_value'] = floatval($inv_record['widthValue']) ? : 1;
			}
			if(array_key_exists('heightValue', $inv_record) && $inv_record['heightValue'] != '' && is_numeric($inv_record['heightValue'])) {
				$single['height_value'] = floatval($inv_record['heightValue']) ? : 1;
			}
			if(array_key_exists('realQtyData', $inv_record) && $inv_record['realQtyData'] != '' && is_numeric($inv_record['realQtyData'])) {
				$single['real_qty_data'] = floatval($inv_record['realQtyData']) ? : $single['qty'];
			}
			if(array_key_exists('devideFactor', $inv_record) && $inv_record['devideFactor'] != '' && is_numeric($inv_record['devideFactor'])) {
				$single['devide_factor'] = floatval($inv_record['devideFactor']) ? : 1;
			}	
			
			array_push($response, $single);
		}

		return $response;
	}
}