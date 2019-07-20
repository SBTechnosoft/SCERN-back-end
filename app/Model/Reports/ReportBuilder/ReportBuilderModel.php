<?php
namespace ERP\Model\Reports\ReportBuilder;

use Illuminate\Database\Eloquent\Model;
use DB;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use stdClass;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class ReportBuilderModel extends Model
{
	/**
	 * get data 
	 * returns the array-data/exception message
	*/
	public function getReportBuilderGroups()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("SELECT
			rb_group_id as rbGroupId,
			rb_group_name as rbGroupName,
			rb_group_category as rbGroupCategory
			FROM reports_rb_groups
			WHERE deleted_at = 0
			AND query_details <> ''
			AND query_summary <> ''
			ORDER BY rb_group_category;
		");
		DB::commit();
		if (count($raw)) {
			return json_encode($raw);
		}
		return $exceptionArray['404'];
	}

	/**
	 * @param groupId
	 * get data
	 * @return array-data/exception message
	 */
	public function getTablesByGroup($groupId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		DB::beginTransaction();
		$raw = DB::connection()->select("SELECT
			reports_rb_tables.rb_table_id as id,
			reports_rb_tables.table_label as label,
			children
			FROM reports_rb_table_group_pivot
			INNER JOIN reports_rb_tables ON reports_rb_tables.rb_table_id = reports_rb_table_group_pivot.rb_table_id
			INNER JOIN (
				SELECT 
					table_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"id\":', field_id,
								', \"label\":\"', IFNULL(field_label, ''), 
								'\", \"type\":\"', data_type,
								'\" }'
							 ) SEPARATOR ', '),
						']'
					) children
				FROM reports_rb_tblfields
				WHERE deleted_at = 0
				AND is_active = 1
				GROUP BY table_id 
			) fields ON fields.table_id = reports_rb_tables.rb_table_id
			WHERE reports_rb_tables.deleted_at = 0
			AND rb_group_id = {$groupId}
		");
		DB::commit();
		if (count($raw)) {
			return json_encode($raw);
		}
		return $exceptionArray['404'];
	}

	/**
	 * @param fields
	 * get data
	 * @return array-data/exception message
	 */
	public function getRequiredTableByFields($fields)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if (!count($fields)) {
			return $exceptionArray['content'];
		}
		$fieldStr = implode(', ', $fields);
		DB::beginTransaction();
		$raw = DB::connection()->select("SELECT
			distinct(table_name)
			FROM
			reports_rb_tables
			JOIN reports_rb_tblfields on reports_rb_tblfields.table_id = reports_rb_tables.rb_table_id
			WHERE field_id IN ({$fieldStr})
		");
		DB::commit();
		if (count($raw)) {
			return $raw;
		}
		return $exceptionArray['404'];
	}

	/**
	 * Get preview data for report builder
	 * @param reportbuilder array
	 * @return report builder preview data
	 */

	public function getPreview($reportBuildArray)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$joinVariables = $constantDatabase->reportBuilderJoin();
		$joinFilters = $constantDatabase->defaultJoinConditions();

		$reportGroupId = $reportBuildArray['report_group'];
		if(!$reportGroupId) {
			return $exceptionArray['content'];
		}
		DB::beginTransaction();
		$raw = DB::connection()->select("SELECT
			query_summary,
			query_details
			FROM reports_rb_groups
			WHERE rb_group_id ='{$reportGroupId}'
		");
		if (count($raw) != 1) {
			return $exceptionArray['content'];
		}
		$query = $reportBuildArray['report_type'] == 'details' ? $raw[0]->query_details : $raw[0]->query_summary;
		$selectFields = array_column($reportBuildArray['columns'], 'id');
		$fieldIn = implode(', ', $selectFields);
		$raw = DB::connection()->select("SELECT
			CONCAT(table_name, '.',field_name,' as field', field_id) as field
			FROM reports_rb_tblfields
			JOIN reports_rb_tables ON reports_rb_tblfields.table_id = reports_rb_tables.rb_table_id
			WHERE field_id IN ($fieldIn)
		");
		if (!count($raw)) {
			return $exceptionArray['content'];
		}
		$selectors = json_decode(json_encode($raw), true);
		$selectQuery = implode(', ', array_column($selectors, 'field'));
		$query = str_replace('|@@|SELECT_FIELDS|@@|', $selectQuery, $query);
		$filterStr = '';
		foreach ($joinVariables as $variable => $joinStr) {
			$joinConcat = '';
			for ($joinIter=0; $joinIter < count($reportBuildArray['joins']); $joinIter++) { 
				if (strpos($joinStr, 'JOIN '.$reportBuildArray['joins'][$joinIter].' ') > -1) {
					$joinConcat = $joinStr;
					if (array_key_exists($variable, $joinFilters)) {
						$filterStr .= $joinFilters[$variable];
					}
					break;
				}
			}
			$query = str_replace('|@@|'.$variable.'|@@|', $joinConcat, $query);
		}

		$filterStr .= $this->createFilters($reportBuildArray['filters']);

		$query = str_replace('|@@|FILTERS|@@|', $filterStr, $query);
		// In case of Having Filter
		$query = str_replace('|@@|HAVINGFILTERS|@@|', '', $query);
		
		$filterFields = array();
		$orderBy = $this->applyOrderBy($reportBuildArray['order_by']);
		$query = str_replace('|@@|ORDERBY_FIELDS|@@|', $orderBy, $query);

		$groupBy = $this->applyGroupBy($reportBuildArray['group_by']);;
		
		$query = str_replace('|@@|GROUP_BY|@@|', $groupBy, $query);
		$raw = DB::connection()->select($query." LIMIT 0,20;");
		if (!count($raw)) {
			return $exceptionArray['404'];
		}
		DB::commit();
		return json_encode($raw);
	}

	private function createFilters($filters)
	{
		//database selection
		// $database = "";
		// $constantDatabase = new ConstantClass();
		// $databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$filterFields = array();
		$filterStr = "";
		foreach ($filters as $filter) {
			$fieldId = $filter['field']['id'];
			if(!array_key_exists($fieldId, $filterFields)) {
				$raw = DB::connection()->select("SELECT
					CONCAT(table_name, '.',field_name) as field,
					data_type
					FROM reports_rb_tblfields
					JOIN reports_rb_tables ON reports_rb_tblfields.table_id = reports_rb_tables.rb_table_id
					WHERE field_id = {$fieldId}
				");
				if (!count($raw)) {
					return $exceptionArray['content'];
				}
				$filterFields[$fieldId] = (array) $raw[0];
			}
			$field_name = $filterFields[$fieldId]['field'];
			

			switch ($filter['conditionType']) {
				case 'EQUALS TO':
					$filterStr .= $field_name." = '{$filter['filterValue']}' AND ";
					break;

				case 'NOT EQUALS TO':
					$filterStr .= $field_name." <> '{$filter['filterValue']}' AND ";
					break;

				case 'STARTS WITH':
					$filterStr .= $field_name." LIKE '{$filter['filterValue']}%' AND ";
					break;

				case 'ENDS WITH':
					$filterStr .= $field_name." LIKE '%{$filter['filterValue']}' AND ";
					break;

				case 'CONTAINS':
					$filterStr .= $field_name." LIKE '%{$filter['filterValue']}%' AND ";
					break;

				case 'NOT CONTAINT':
					$filterStr .= $field_name." NOT LIKE '%{$filter['filterValue']}%' AND ";
					break;

				case 'GREATER THAN':
					$filterStr .= $field_name." > '{$filter['filterValue']}' AND ";
					break;

				case 'LESS THAN':
					$filterStr .= $field_name." < '{$filter['filterValue']}' AND ";
					break;

				case 'GREATER OR EQUALS TO':
					$filterStr .= $field_name." >= '{$filter['filterValue']}' AND ";
					break;

				case 'LESS OR EQUALS TO':
					$filterStr .= $field_name." <= '{$filter['filterValue']}' AND ";
					break;
				case 'BEFORE':
					$filter['filterValue'] = Carbon::createFromFormat('d-m-Y', $filter['filterValue']);
					$filterStr .= $field_name." > '{$filter['filterValue']}' AND ";
					break;

				case 'AFTER':
					$filter['filterValue'] = Carbon::createFromFormat('d-m-Y', $filter['filterValue']);
					$filterStr .= $field_name." < '{$filter['filterValue']}' AND ";
					break;

				case 'DATE EQUALS':
					$filter['filterValue'] = Carbon::createFromFormat('d-m-Y', $filter['filterValue']);
					$filterStr .= $field_name." = '{$filter['filterValue']}' AND ";
					break;

				case 'MONTH EQUALS':
					$monthArray = explode('-', $filter['filterValue']);
					$month = $monthArray[0];
					$year = $monthArray[1];
					$filterStr .= " MONTH({$field_name}) = '{$month}' AND YEAR({$field_name}) = '{$year}' AND ";
					break;

				case 'YEAR EQUALS':
					$filterStr .= "YEAR({$field_name}) = '{$filter['filterValue']}' AND ";
					break;

				default:
					$filterStr .= "";
					break;
			}
		}
		return $filterStr;
	}

	private function applyOrderBy($fieldId)
	{
		$orderBy = "";
		$raw = DB::connection()->select("SELECT
			CONCAT(table_name, '.',field_name) as field,
			data_type
			FROM reports_rb_tblfields
			JOIN reports_rb_tables ON reports_rb_tblfields.table_id = reports_rb_tables.rb_table_id
			WHERE field_id = {$fieldId}
		");
		if (!count($raw)) {
			return $exceptionArray['content'];
		}
		$orderBy = "ORDER BY {$raw[0]->field} ASC";
		return $orderBy;
	}

	private function applyGroupBy($fieldId)
	{
		$groupBy = "";
		if ($fieldId) {
			$raw = DB::connection()->select("SELECT
				CONCAT(table_name, '.',field_name) as field,
				data_type
				FROM reports_rb_tblfields
				JOIN reports_rb_tables ON reports_rb_tblfields.table_id = reports_rb_tables.rb_table_id
				WHERE field_id = {$fieldId}
			");
			if (!count($raw)) {
				return $exceptionArray['content'];
			}
			$groupBy = "GROUP BY {$raw[0]->field}";
		}
		return $groupBy;
	}
}