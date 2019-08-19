<?php
namespace ERP\Core\Accounting\DebitNotes\Services;

use ERP\Model\Accounting\DebitNotes\DebitNoteModel;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class DebitNoteService
{
	private $model;

	function __construct() {
		$this->model = new DebitNoteModel();
	}
	/**
	 * @param [Persistable $persistable] Object with Name as Array
	 * @return Exception Message
	 */

	function insert($insertPersistable)
	{
		$nameArray = $insertPersistable[0];
		$persistable = $insertPersistable[1];

		$insertArray = array();

		foreach ($nameArray as $key => $getData) {
			$insertArray[$key] = $persistable->$getData();
		}
		return $this->model->insertData($insertArray);
	}
}