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
			CONCAT(table_name, '.',field_name,' as ', field_id) as field
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
		print_r($query);
		die;
		foreach ($joinVariables as $variable => $joinStr) {
			
		}
		
		DB::commit();
	}
}