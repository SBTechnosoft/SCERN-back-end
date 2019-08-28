<?php
namespace ERP\Model\Accounting\SalesReturns;

use Illuminate\Database\Eloquent\Model;
use DB;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use Exception;
/** 
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class SaleReturnInventoryModel extends Model
{
	protected $table = 'sale_return_inventory_dtl';

	function __construct()
	{
		parent::__construct();

		$exceptions = new ExceptionMessage();
		$this->messages = $exceptions->messageArrays();
		$this->constant = new ConstantClass();
		$this->constantVars = $this->constant->constantVariable();
		$database = $this->constant->constantDatabase();
		$this->database = DB::connection($database);
	}
	public function insertData($invData) {

		if(!count($invData)) {
			return $this->messages['204'];
		}
		$Keys = array_keys($invData[0]);
		$dtlValues = call_user_func_array('array_merge', array_map(function($br) use($Keys) {
			$ar = array();
			foreach ($Keys as $key => $value) {
				$ar[$key] = $br[$value];
			}
			return $ar;
		}, $invData));
		$keyString = implode(',', $Keys);
		$singleValueStr = '?';
		$singleValueStr .= str_repeat(', ?', count($Keys) - 1);
		$valueStr = "({$singleValueStr})";
		$valueStr .= str_repeat(", ({$singleValueStr})", count($invData) - 1);
		DB::beginTransaction();
		try {
			$status = $this->database->statement("INSERT INTO {$this->table} ({$keyString}) VALUES {$valueStr};", $dtlValues);
			if($status) {
				DB::commit();
				return $this->messages['200'];
			}
			throw new Exception($this->messages['500']);
		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}
	}
	public function deleteData($conditions)
	{
		if(!count($conditions)) {
			return $this->messages['204'];
		}
		$whereStr = "1";
		$whereArray = array();
		foreach ($conditions as $key => $value) {
			$whereStr .= " AND {$key} = ?";
			array_push($whereArray, $value);
		}

		DB::beginTransaction();
		$status = $this->database->statement("DELETE FROM {$this->table} WHERE {$whereStr};", $whereArray);
		DB::commit();
	}
}
