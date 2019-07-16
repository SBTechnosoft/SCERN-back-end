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
     * get all the data between given date and call the model for database selection opertation
     * @param (no params)
     * @return array-data/exception message
     */
    public function getReportBuilderGroups()
    {
    	$reportBuilderModel = new ReportBuilderModel();
    	return $reportBuilderModel->getReportBuilderGroups();
    }

    /**
     * get all the data between given date and call the model for database selection opertation
     * @param (groupId)
     * @return array-data/exception message
     */
    public function getTablesByGroup($groupId)
    {
    	$reportBuilderModel = new ReportBuilderModel();
    	return $reportBuilderModel->getTablesByGroup($groupId);
    }
}