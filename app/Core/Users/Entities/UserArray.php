<?php
namespace ERP\Core\Users\Entities;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class UserArray 
{
    public function userSearching()
	{
		$userSearch = array();
		$userSearch['companyid'] = 'company_id';
		$userSearch['branchid'] = 'branch_id';
		return $userSearch;
	}
}