<?php
namespace ERP\Core\Entities;

use ERP\Core\Companies\Services\CompanyService;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CompanyDetail extends CompanyService 
{
	public function getCompanyDetails($companyId)
	{
		//get the city_name from database
		$encodeCompanyDataClass = new CompanyDetail();
		$companyStatus = $encodeCompanyDataClass->getCompanyData($companyId);
		$companyDecodedJson = json_decode($companyStatus,true);
		return $companyDecodedJson;
	}
    
}