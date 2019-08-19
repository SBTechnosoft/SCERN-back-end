<?php
namespace ERP\Model\Accounting\CreditNotes;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use Exception;
/** 
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CreditNoteModel extends Model
{
	/**
	 * @var $messages
	 * @var $constant
	 * @var $constantVars
	 * @var $table
	 */
	private $messages;
	private $constant;
	private $constantVars;
	private $database;
	protected $table = 'credit_note';
	protected $child_table = 'credit_note_dtl';
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

	function insertData($insertArray) {
		$now = Carbon::now();
		$dtl_array = $insertArray['credit_array'];
		$insertArray = array_except($insertArray, ['credit_array']);
		$insertArray['created_at'] = $now;
		$insertArray['entry_date'] = Carbon::createFromFormat('d-n-Y', trim($insertArray['entry_date']))->format('Y-m-d');
		$insertKeys = array_keys($insertArray);
		$insertValues = array_values($insertArray);
		if(count($insertKeys) == 0 || count($insertValues) == 0 || count($insertKeys) != count($insertValues) || count($dtl_array) == 0) {
			return $this->messages['content'];
		}

		DB::beginTransaction();
		try {
			$keyStr = implode(',', $insertKeys);
			$valueStr = "?";
			$valueStr .= str_repeat(', ?', count($insertValues) - 1);

			$status = $this->database->statement("INSERT INTO {$this->table} ({$keyStr}) VALUES ({$valueStr});", $insertValues);
			if(!$status) {
				throw new Exception($this->messages['500']);
			}
			$raw = $this->database->select("SELECT LAST_INSERT_ID() as insertId;");
			if(!count($raw)) {
				throw new Exception($this->messages['500']);
			}
			$creditId = $raw[0]->insertId;
			
			$dtlValues = call_user_func_array('array_merge', array_map(function($br) use($creditId, $now) {
				$ar = array();
				$ar[0] = $creditId;
				$ar[1] = $br['client_id'];
				$ar[2] = $br['client_name'];
				$ar[3] = $br['ledger_id'];
				$ar[4] = $br['amount'];
				$ar[5] = $now;
				return $ar;
			}, $dtl_array));

			$dtlKey = 'credit_id, client_id, client_name, ledger_id, amount, created_at';
			$dtlStr = str_repeat("(?, ?, ?, ?, ?, ?), ", count($dtl_array) - 1);
			$status = $this->database->statement("INSERT INTO {$this->child_table} ({$dtlKey}) VALUES {$dtlStr} (?, ?, ?, ?, ?, ?);", $dtlValues);

			if(!$status) {
				throw new Exception($this->messages['500']);
			}
			DB::commit();
			return $this->messages['200'];

		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}
	}
}