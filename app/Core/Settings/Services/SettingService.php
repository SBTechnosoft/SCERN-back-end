<?php
namespace ERP\Core\Settings\Services;

// use ERP\Core\Settings\Templates\Persistables\TemplatePersistable;
// use ERP\Core\Settings\Templates\Entities\Branch;
use ERP\Model\Settings\SettingModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
// use ERP\Core\Settings\Entities\Setting;
// use ERP\Core\Settings\Templates\Entities\EncodeData;
use ERP\Core\Settings\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class SettingService extends AbstractService
{
    /**
     * @var settingService
	 * $var settingModel
     */
    private $settingService;
    private $settingModel;
	
    /**
     * @param SettingService $settingService
     */
    public function initialize(SettingService $settingService)
    {		
		echo "init";
    }
	
    /**
     * @param SettingPersistable $persistable
     */
    public function create(SettingPersistable $persistable)
    {
		return "create method of SettingService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param SettingPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$settingArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$settingArray = func_get_arg(0);
		
		for($data=0;$data<count($settingArray);$data++)
		{
			$funcName[$data] = $settingArray[$data][0]->getName();
			$getData[$data] = $settingArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $settingArray[$data][0]->getkey();
		}
		
		//data pass to the model object for insert
		$settingModel = new SettingModel();
		$status = $settingModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * call the model for getting the data from database and convert the appropriate data 
     * @return array-data/error-message
     */
	public function getData()
	{
		//data pass to the model object for insert
		$settingModel = new SettingModel();
		$status = $settingModel->getAllData();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($exceptionArray['204'],$status)==0)
		{
			return $status;
		}
		else
		{
			$encodedAllData = new EncodeAllData();
			$result = $encodedAllData->getEncodedAllData($status);
			return $result;
		}
	}
	
	/**
     * get the data from persistable object and call the model for database update opertation
     * @param SettingPersistable $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$settingArray = array();
		$getData = array();
		$funcName = array();
		$settingArray = func_get_arg(0);
		
		for($data=0;$data<count($settingArray);$data++)
		{
			$funcName[$data] = $settingArray[$data][0]->getName();
			$getData[$data] = $settingArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $settingArray[$data][0]->getkey();
		}
		// data pass to the model object for update
		$settingModel = new SettingModel();
		$status = $settingModel->updateData($getData,$keyName);
		return $status;	
	}

	/**
     * Template Column setting for Color/Size/Framework and Advance Mou
     * @return html
    */
    public function settingTemplateColumns()
    {
		/* Setting */
			$setting_advanceMou = false;
			$setting_color = false;
			$setting_size = false;
			$setting_frameNo = false;

			$settingData = $this->getData();
			$settingData = json_decode($settingData);

			$stCount = count($settingData);
			$stIndex = 0;
			while ($stIndex < $stCount) {
				$settingSingleData = $settingData[$stIndex];

				if($settingSingleData->settingType == 'product')
				{
					if ($settingSingleData->productAdvanceMouStatus == 'enable') {
						$setting_advanceMou = true;
					}
					if ($settingSingleData->productColorStatus == 'enable') {
						$setting_color = true;
					}
					if ($settingSingleData->productSizeStatus == 'enable') {
						$setting_size = true;
					}
					if ($settingSingleData->productFrameNoStatus == 'enable') {
						$setting_frameNo = true;
					}
					if ($settingSingleData->productMrpRequireStatus == 'enable') {
						$setting_mrp = true;
					}
					if ($settingSingleData->productMarginStatus == 'enable') {
						$setting_margin = true;
					}
					if ($settingSingleData->productVariantStatus == 'enable') {
						$setting_variant = true;
					}
					break;
				}
				$stIndex++;
			}
		/* End Setting */

		
		$htmlTh = "";
		if ($setting_advanceMou == true)
		{
			$htmlTh = "<td class='tg-m36b theqp' style='font-size: 12px; padding: 2px; height: 15px; text-align: center; border: 1px solid black; border-right: 0px; overflow-wrap: break-word; max-width: 100px;' colspan='3' rowspan='2'><strong>MOU</strong></td>";
		}
		$color_size_colspan = 3;
		if($setting_frameNo == true){
			$color_size_colspan = 2;
		}

		if ($setting_color == true && $setting_size == true )
		{
			$htmlTh .= "<td class='tg-m36b theqp' style='font-size: 12px; padding: 2px; height: 15px; text-align: center; border: 1px solid black; border-right: 0px; overflow-wrap: break-word; max-width: 100px;' colspan='".$color_size_colspan."' rowspan='2'><strong>Color | Size</strong></td>";
		} 
		else if ($setting_color == true) 
		{
			$htmlTh .= "<td class='tg-m36b theqp' style='font-size: 12px; padding: 2px; height: 15px; text-align: center; border: 1px solid black; border-right: 0px; overflow-wrap: break-word; max-width: 100px;' colspan='".$color_size_colspan."' rowspan='2'><strong>Color</strong></td>";
		} 
		else if ($setting_size == true)
		{
			$htmlTh .= "<td class='tg-m36b theqp' style='font-size: 12px; padding: 2px; height: 15px; text-align: center; border: 1px solid black; border-right: 0px; overflow-wrap: break-word; max-width: 100px;' colspan='".$color_size_colspan."' rowspan='2'><strong>Size</strong></td>";
		}

		if ($setting_frameNo == true)
		{
			if ($setting_color == true || $setting_size == true) 
			{
				$htmlTh .= "<td class='tg-m36b theqp' style='font-size: 12px; padding: 2px; height: 15px; text-align: center; border: 1px solid black; border-right: 0px; overflow-wrap: break-word; max-width: 100px;' colspan='1' rowspan='2'><strong>Frame No</strong></td>";
			}
			else
			{
				$htmlTh = "<td class='tg-m36b theqp' style='font-size: 12px; padding: 2px; height: 15px; text-align: center; border: 1px solid black; border-right: 0px; overflow-wrap: break-word; max-width: 100px;' colspan='3' rowspan='2'><strong>Frame No</strong></td>";
			}
		}

		return $htmlTh;
	}
	
	/**
     * get and invoke method is of Container Interface method
     * @param int $id,$name
     */
    public function get($id,$name)
    {
		echo "get";		
    }   
	public function invoke(callable $method)
	{
		echo "invoke";
	}   
}