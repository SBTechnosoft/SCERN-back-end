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
}