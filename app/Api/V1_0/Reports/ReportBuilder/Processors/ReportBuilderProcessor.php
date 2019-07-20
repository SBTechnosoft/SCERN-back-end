<?php
namespace ERP\Api\V1_0\Reports\ReportBuilder\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Exceptions\ExceptionMessage;
use ERP\Api\V1_0\Reports\ReportBuilder\Transformers\ReportBuilderTransformer;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class ReportBuilderProcessor extends BaseProcessor
{
	/**
     * @var settingPersistable
	 * @var request
     */

	/**
     * get the form-data and set into the persistable object
     * @param Request object [Request $request]
     * @return preview Array / Error Message Array / Exception Message
     */	
	public function previewProcess(Request $request)
	{
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		if (!count($request->all())) {
			return $exceptionArray['204'];
		}
		$reportBuilderTransformer = new ReportBuilderTransformer();
		$trimRequest = $reportBuilderTransformer->trimPreview($request);
		if (!is_array($trimRequest)) {
			return $trimRequest;
		}
		$selectedColumns = array_column($trimRequest['columns'], 'id');
		$filterColumns = array_column(array_column($trimRequest['filters'] , 'field'), 'id');
		array_push($filterColumns, $trimRequest['group_by'], $trimRequest['order_by']);
		$columns = array_values(array_unique(array_merge($selectedColumns, $filterColumns)));
		$trimRequest['join_columns'] = $columns;
		return $trimRequest;
	}

	/**
	 * @param Request Object [Request $request]
	 * @return report template Array / Exception Message
	 */
	public function storeProcess(Request $request)
	{
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		if (!count($request->all())) {
			return $exceptionArray['204'];
		}
		$reportBuilderTransformer = new ReportBuilderTransformer();
		return $reportBuilderTransformer->trimStore($request);
	}
}