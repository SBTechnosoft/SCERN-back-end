<?php
namespace ERP\Api\V1_0\Accounting\CreditNotes\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
// Common deps
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
// Credit note deps
use ERP\Api\V1_0\Accounting\CreditNotes\Transformers\CreditNoteTransformer;
use ERP\Core\Accounting\CreditNotes\Persistables\CreditNotePersistable;
// Journal deps
use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Api\V1_0\Accounting\Journals\Controllers\JournalController;
use Illuminate\Container\Container;
use ERP\Core\Accounting\Bills\Entities\SalesTypeEnum;
use ERP\Core\Accounting\Journals\Entities\AmountTypeEnum;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CreditNoteProcessor extends BaseProcessor
{
	/**
     * @var persistable
	 * @var request
     */
	private $persistable;
	private $request;
	private $constant;
	private $constantVars;
	private $messages;
	private $transform;
	
	function __construct(Request $request) {
		$this->request = $request;
		$this->persistable = new CreditNotePersistable();

		$this->constant = new ConstantClass();
		$this->constantVars = $this->constant->constantVariable();
		// get exception message
		$this->exception = new ExceptionMessage();
		$this->messages = $this->exception->messageArrays();

		$this->transform = new CreditNoteTransformer();
		// $this->validate = new
	}
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Creditnote Persistable object
     */	
    public function createPersistable($saleData)
	{
		$trimRequest = $this->transform->trimInsertData($this->request);
		if(!is_array($trimRequest)) {
			return $trimRequest;
		}
		if($saleData['balance'] < $trimRequest['total']) {
			return $this->messages['invalidAmount'];
		}

		$trimRequest['sale_id'] = $saleData['sale_id'];
		$trimRequest['company_id'] = $saleData['company_id'];

		$jfId = $this->makeJournalArray($trimRequest);

		if(!is_numeric($jfId)) {
			return $jfId;
		}
		$trimRequest['jf_id'] = $jfId;

		$getNameArray = array();
		foreach ($trimRequest as $key => $value) {
			if(is_numeric($key)) {
				continue;
			}
			$functionName = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
			$setName = 'set'.$functionName;
			$getNameArray[$key] = 'get'.$functionName;
			$this->persistable->$setName($value);
		}
		return array($getNameArray, $this->persistable);
	}

	private function makeJournalArray($tRequest, $jfId = 0) {

		$journalController = new JournalController(new Container());
		$operation = 'update';
		if($jfId == 0) {
			$journalMethod=$this->constantVars['getMethod'];
			$journalPath=$this->constantVars['journalUrl'];
			$journalDataArray = array();
			$journalJfIdRequest = Request::create($journalPath,$journalMethod,$journalDataArray);
			$status = $journalController->getData($journalJfIdRequest);
			$jfId = json_decode($status)->nextValue;
			$operation = 'insert';
		}
		$saleTypeEnum = new SalesTypeEnum();
		$salesType = $saleTypeEnum->enumArrays();

		$amountTypeEnum = new AmountTypeEnum();
		$amountTypeArray = $amountTypeEnum->enumArrays();
		$ledgerModel = new LedgerModel();
		$ledgerIdData = $ledgerModel->getLedgerId($tRequest['company_id'],$salesType['wholesales']);
		$decodedLedgerId = json_decode($ledgerIdData);
		$salesLedger = $decodedLedgerId[0]->ledger_id;

		$journalArray = array();

		$method=$this->constantVars['postMethod'];
		$path=$this->constantVars['journalUrl'];

		foreach ($tRequest['credit_array'] as $key => $creditArray) {
			$journalEntry = array(
				array(
					'amount' => $creditArray['amount'],
					'amountType' => $amountTypeArray['debitType'],
					'ledgerId' => $salesLedger
				),
				array(
					'amount' => $creditArray['amount'],
					'amountType' => $amountTypeArray['creditType'],
					'ledgerId' => $creditArray['ledger_id']
				)
			);

			$journalArray= array(
				'jfId' => $jfId,
				'data' => $journalEntry,
				'entryDate' => $tRequest['entry_date'],
				'companyId' => $tRequest['company_id']
			);

			$journalRequest = Request::create($path,$method,$journalArray);
			$journalRequest->headers->set('authenticationtoken',$this->request->header()['authenticationtoken'][0]);
			$processedData = $journalController->store($journalRequest);
			if (strcmp($processedData, $this->messages['200']) != 0) {
				return $processedData;
			}
		}

		return $jfId;
	}
}