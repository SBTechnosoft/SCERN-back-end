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
	 * get all stored reports
	 * @param (no params)
	 * @return array -data / exception message
	 */
	public function getAllData()
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
			report_id as reportId,
			report_name as reportName,
			report_title as reportTitle,
			title_alignment as titlePosition
			FROM reports_rb_mst
			WHERE deleted_at = 0;
		");
		DB::commit();
		if (count($raw)) {
			return json_encode($raw);
		}
		return $exceptionArray['404'];
	}
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

		$reportGroupId = $reportBuildArray['report_group'];
		if(!$reportGroupId) {
			return $exceptionArray['content'];
		}
		DB::beginTransaction();
		$raw = DB::connection()->select("SELECT
			query_summary,
			query_details
			FROM reports_rb_groups
			WHERE rb_group_id = ? ", [$reportGroupId]);
		if (count($raw) != 1) {
			return $exceptionArray['content'];
		}

		$tables = $this->getRequiredTableByFields($reportBuildArray['join_columns']);
		if (!is_array($tables) && !count($tables)) {
			return $tables;
		}

		$joinTables = array_column(json_decode(json_encode($tables), true), 'table_name');
		if (!count($joinTables)) {
			return $exceptionArray['404'];
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

		$query = $this->resolveJoins($query, $joinTables);
		
		$filterStr = $this->createFilters($reportBuildArray['filters']);

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

	/**
	 * @param $reportId
	 * @return array-data / exception message
	 */
	public function generate($reportId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$database = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		DB::beginTransaction();
		$raw = DB::connection($database)->select("SELECT
			reports_rb_mst.report_type,
			reports_rb_mst.order_by,
			reports_rb_mst.group_by,
			reports_rb_groups.query_summary,
			reports_rb_groups.query_details
			FROM reports_rb_mst
			INNER JOIN reports_rb_groups ON reports_rb_mst.rb_group_id = reports_rb_groups.rb_group_id
			WHERE reports_rb_mst.report_id = ? AND
			reports_rb_mst.deleted_at = 0 AND
			reports_rb_groups.deleted_at = 0
			", [$reportId]);
		if (count($raw) != 1) {
			return $exceptionArray['content'];
		}
		$query = $raw[0]->report_type == 'details' ? $raw[0]->query_details : $raw[0]->query_summary;
		$orderBy = $raw[0]->order_by;
		$groupBy = $raw[0]->group_by;
		$raw = DB::connection($database)->select("SELECT
			field_id,
			field_label,
			position
			FROM reports_rb_selects
			WHERE report_id = ?
		", [$reportId]);
		if (!count($raw)) {
			return $exceptionArray['content'];
		}
		$join_columns = array();
		$fields = json_decode(json_encode($raw), true);
		$join_columns = array_column('field_id', $fields);
		array_push($join_columns, $orderBy, $groupBy);

		$raw = DB::connection($database)->select("SELECT
			field_id,
			filter_type as conditionType,
			filter_value as filterValue
			FROM reports_rb_filters
			WHERE report_id = ?
			",[$reportId]);
		$filters = json_decode(json_encode($raw, true);
		$filterColumns = array_column($filters, 'field_id');
		$join_columns = array_values(array_unique(array_merge($join_columns, $filterColumns)));
			
		$tables = $this->getRequiredTableByFields($join_columns);
		if (!is_array($tables) && !count($tables)) {
			return $tables;
		}
		
	}
	/**
	 * @param trimmed request of report template
	 * @return status / exception message
	 */
	public function insertData($reportTemplate)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		if(!$reportTemplate['report_group_id']) {
			return $exceptionArray['content'];
		}
		DB::beginTransaction();
		try {
			$raw = DB::connection($databaseName)->select("SELECT
				query_summary,
				query_details
				FROM reports_rb_groups
				WHERE rb_group_id = ? ;", [$reportTemplate['report_group_id']]);
			if (count($raw) != 1) {
				throw new Exception($exceptionArray['content']);
			}
			$storeArray = array();
			$storeArray[0] = $reportTemplate['report_group_id'];
			$storeArray[1] = $reportTemplate['report_name'];
			$storeArray[2] = $reportTemplate['report_title'];
			$storeArray[3] = $reportTemplate['title_alignment'];
			$storeArray[4] = $reportTemplate['report_type'];
			$storeArray[5] = $reportTemplate['order_by'];
			$storeArray[6] = $reportTemplate['group_by'];
			$store = DB::connection($databaseName)->statement("INSERT INTO reports_rb_mst (rb_group_id, report_name, report_title, title_alignment, report_type, order_by, group_by) VALUES(?, ?, ?, ?, ?, ?, ?);", $storeArray);
			if (!$store) {
				throw new Exception($exceptionArray['500']);
			}
			$raw = DB::connection($databaseName)->select("SELECT LAST_INSERT_ID() as insertId;");
			if(!count($raw)) {
				throw new Exception($exceptionArray['500']);
			}
			$reportId = $raw[0]->insertId;
			$selectStore = call_user_func_array('array_merge', array_map(function($ar) use ($reportId) {
				$b = array();
				$b[0] = $reportId;
				$b[1] = $ar['id'];
				$b[2] = addslashes($ar['label']);
				$b[3] = $ar['position'];
				return $b;
			}, $reportTemplate['columns']));
			$preValue = str_repeat(',(?, ?, ?, ?)', count($reportTemplate['columns']) - 1);
			$store = DB::connection($databaseName)->statement("INSERT INTO reports_rb_selects (report_id, field_id, field_label, position) VALUES (?, ?, ?, ?)".$preValue.";", $selectStore);
			if (!$store) {
				throw new Exception($exceptionArray['500']);
			}
			$filterStore = call_user_func_array('array_merge', array_map(function($ar) use ($reportId) {
				$b = array();
				$b[0] = $reportId;
				$b[1] = $ar['field_id'];
				$b[2] = $ar['conditionType'];
				$b[3] = addslashes($ar['filterValue']);
				return $b;
			}, $reportTemplate['filters']));
			$preValue = str_repeat(',(?, ?, ?, ?)', count($reportTemplate['filters']) - 1);
			$store = DB::connection($databaseName)->statement("INSERT INTO reports_rb_filters (report_id, field_id, filter_type, filter_value) VALUES (?, ?, ?, ?)".$preValue.";", $filterStore);

			if (!$store) {
				throw new Exception($exceptionArray['500']);
			}
			DB::commit();
			return $exceptionArray['200'];
		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}
	}
	/**
	 * @param fields
	 * get data
	 * @return array-data/exception message
	 */
	private function getRequiredTableByFields($fields)
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
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$orderBy = "";
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
			$orderBy = "ORDER BY {$raw[0]->field} ASC";
		}
		return $orderBy;
	}

	private function applyGroupBy($fieldId)
	{
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

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

	private function resolveJoins($query, $joins)
	{
		$constants = new ConstantClass();
		$joinVariables = $constants->reportBuilderJoin();
		$joinFilters = $constants->defaultJoinConditions();
		$filterStr = '';

		foreach ($joinVariables as $variable => $joinStr) {
			$joinConcat = '';
			for ($joinIter=0; $joinIter < count($joins); $joinIter++) { 
				if (strpos($joinStr, 'JOIN '.$joins[$joinIter].' ') > -1) {
					$joinConcat = $joinStr;
					if (array_key_exists($variable, $joinFilters)) {
						$filterStr .= $joinFilters[$variable];
					}
					break;
				}
			}
			$query = str_replace('|@@|'.$variable.'|@@|', $joinConcat, $query);
		}
		$filterStr .= '|@@FILTERS|@@|';
		$query = str_replace('|@@FILTERS|@@|', $filterStr, $query);
		return $query;
	}
}