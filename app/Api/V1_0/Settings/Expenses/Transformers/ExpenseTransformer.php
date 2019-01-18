<?php
namespace ERP\Api\V1_0\Settings\Expenses\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Products\Entities\EnumClasses\DiscountTypeEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ExpenseTransformer
{
	/**
     * @param Request Object
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		//data get from body
		$expenseType = trim($request->input('expenseType')); 
		$expenseTypeFlag=0;
		$discountTypeEnum = new DiscountTypeEnum();
		$discountTypeArray = $discountTypeEnum->enumArrays();
		//check discount-type enum
		foreach ($discountTypeArray as $key => $value)
		{
			if(strcmp($value,$expenseType)==0)
			{
				$expenseTypeFlag=1;
				break;
			}
		}
		if($expenseTypeFlag==0)
		{
			return "1";	
		}
		else
		{
			//make an array
			$data = array();
			$data['expense_name'] = trim($request->input('expenseName'));
			$data['expense_type'] = $expenseType;
			$data['expense_value'] = trim($request->input('expenseValue'));
			$data['company_id'] = trim($request->input('companyId'));
			return $data;
		}
	}
	
    /**
     * @param Request Object
     * @return array
     */
	public function trimUpdateData()
	{
		$tExpenseArray = array();
		$expenseValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		for($asciiChar=0;$asciiChar<strlen($keyValue);$asciiChar++)
		{
			if(ord($keyValue[$asciiChar])<=90 && ord($keyValue[$asciiChar])>=65) 
			{
				$convertedValue1 = "_".chr(ord($keyValue[$asciiChar])+32);
				$convertedValue=$convertedValue.$convertedValue1;
			}
			else
			{
				$convertedValue=$convertedValue.$keyValue[$asciiChar];
			}
		}
		$expenseValue = func_get_arg(1);
		for($data=0;$data<count($expenseValue);$data++)
		{
			$tExpenseArray[$data]= array($convertedValue=> trim($expenseValue));
		}
		return $tExpenseArray;
	}
}