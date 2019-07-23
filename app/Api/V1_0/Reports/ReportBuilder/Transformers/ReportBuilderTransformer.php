<?php
namespace ERP\Api\V1_0\Reports\ReportBuilder\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class ReportBuilderTransformer
{
	/**
     * @param Request Object
     * @return array / Exception Message
     */
	public function trimPreview(Request $request)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$response = array();

		if (!array_key_exists('columns', $request->input()) ||
			 !array_key_exists('headers', $request->input()) || 
			 !array_key_exists('filters', $request->input())) 
		{
			return $exceptionArray['content'];
		}
		$headers = json_decode($request->input('headers'), true);
		$filters = json_decode($request->input('filters'), true);
		$columns = json_decode($request->input('columns'), true);
		if (!is_array($columns) || !is_array($filters) || !is_array($headers)) {
			return $exceptionArray['content'];
		}
		if (!array_key_exists('reportGroup', $headers) || !array_key_exists('reportType', $headers) || !array_key_exists('rbGroupId', $headers['reportGroup'])) {
			return $exceptionArray['content'];
		}
		$response['filters']= $filters;
		$response['report_group'] = $headers['reportGroup']['rbGroupId'];
		$response['report_type'] = $headers['reportType'];
		$response['columns'] = $columns;
		$group_by = array_key_exists('groupBy', $request->input()) ? json_decode($request->input('groupBy'), true) : array();
		$response['group_by'] = array_key_exists('id', $group_by) ? $group_by['id'] : 0;
		$order_by = array_key_exists('orderBy', $request->input()) ? json_decode($request->input('orderBy'), true) : array();
		$response['order_by'] = array_key_exists('id', $order_by) ? $order_by['id'] : 0;
		return $response;
	}
	/**
	 * @param Request Object
	 * @return array / Exception Message
	 */
	public function trimStore(Request $request)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$response = array();

		if (!array_key_exists('columns', $request->input()) ||
			 !array_key_exists('headers', $request->input()) || 
			 !array_key_exists('filters', $request->input())) 
		{
			return $exceptionArray['content'];
		}
		$headers = json_decode($request->input('headers'), true);
		$filters = json_decode($request->input('filters'), true);
		$columns = json_decode($request->input('columns'), true);
		if (!is_array($columns) || !is_array($filters) || !is_array($headers) || !count($columns)) {
			return $exceptionArray['content'];
		}
		if (!array_key_exists('reportGroupId', $headers) || 
			!array_key_exists('reportType', $headers) || 
			!array_key_exists('reportName', $headers) || 
			!array_key_exists('reportTitle', $headers)) 
		{
			return $exceptionArray['content'];
		}

		$response['filters']= $filters;
		$response['report_group_id'] = $headers['reportGroupId'];
		$response['report_type'] = $headers['reportType'];
		$response['report_name'] = $headers['reportName'];
		$response['report_title'] = $headers['reportTitle'];
		$response['title_alignment'] = $headers['position'];
		$response['columns'] = $columns;
		$group_by = array_key_exists('groupBy', $request->input()) ? json_decode($request->input('groupBy'), true) : array();
		$response['group_by'] = array_key_exists('id', $group_by) ? $group_by['id'] : 0;
		$order_by = array_key_exists('orderBy', $request->input()) ? json_decode($request->input('orderBy'), true) : array();
		$response['order_by'] = array_key_exists('id', $order_by) ? $order_by['id'] : 0;
		
		return $response;
	}
}