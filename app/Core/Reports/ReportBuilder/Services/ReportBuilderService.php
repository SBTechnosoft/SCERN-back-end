<?php 
namespace ERP\Core\Reports\ReportBuilder\Services;
use ERP\Core\Support\Service\AbstractService;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Reports\ReportBuilder\ReportBuilderModel;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class ReportBuilderService extends AbstractService
{
	/**
	 * @var ReportBuilderService
	 * $var ReportBuilderModel
	 */
	private $reportBuilderService;
	private $reportBuilderModel;

	 /**
	 * @param ReportBuilderService $reportBuilderService
	 */
	 public function initialize(ReportBuilderService $reportBuilderService)
	 {		
	 	echo "init";
	 }

	/**
	 * @param ReportBuilderPersistable $persistable
	 */
	public function create()
	{
		return "create method of ReportBuilderService";
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

	/**
	 * get all the data
	 * @param (no params)
	 * @return array-data/exception message
	 */
	public function getAllData()
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->getAllData();
	}

	/**
	 * get specific data
	 * @param $reportId
	 * @return array-data/exception message
	 */
	public function getData($reportId)
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->getData($reportId);
	}

	/**
	 * get all the data
	 * @param (no params)
	 * @return array-data/exception message
	 */
	public function getReportBuilderGroups()
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->getReportBuilderGroups();
	}

	/**
	 * get the data by id
	 * @param (groupId)
	 * @return array-data/exception message
	 */
	public function getTablesByGroup($groupId)
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->getTablesByGroup($groupId);
	}

	/**
	 * get preview data
	 * @param trimmed (Request)
	 * @return  array-data/exception message
	 */
	public function preview($builderArray)
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->getPreview($builderArray);
	}

	/**
	 * store report Template data
	 * @param trimmer (Request)
	 * @return staus / exception message
	 */
	public function storeService($reportTemplate)
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->insertData($reportTemplate);
	}

	/**
	 * store report Template data
	 * @param trimmer (Request)
	 * @return staus / exception message
	 */
	public function updateService($reportTemplate, $reportId)
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->updateData($reportTemplate, $reportId);
	}

	/**
	 * @param $reportId
	 * @return array-data/ exception message
	 */
	public function generate($reportId)
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->generate($reportId);
	}

	/**
	 * @param $reportId
	 * @return array-data/ exception message
	 */
	public function destroy($reportId)
	{
		$reportBuilderModel = new ReportBuilderModel();
		return $reportBuilderModel->deleteData($reportId);
	}
}