<?php 
namespace ERP\Core\Reports\ReportBuilder\Services;
use ERP\Core\Support\Service\AbstractService;
use ERP\Exceptions\ExceptionMessage;

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
     * get all the data between given date and call the model for database selection opertation
     * @return status
     */
    public function getReportBuilderGroups()
    {
    	
    }
}