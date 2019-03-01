<?php
namespace ERP\Api\V1_0\Merge\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Merge\Persistables\SettingPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Merge\Validations\SettingValidate;
use ERP\Api\V1_0\Merge\Transformers\SettingTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class MergeProcessor extends BaseProcessor
{
	/**
     * @var settingPersistable
	 * @var request
     */
	private $settingPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return setting Array / Error Message Array / Exception Message
     */	
    public function createPersistable(Request $request)
	{
	}
}