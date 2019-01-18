<!DOCTYPE html>




<html ng-app="myapp">



<head>

	<title> </title>

	

</head>





	<body>

		<center>

			<form id="myform" enctype="multipart/formdata" method="POST" ng-controller="HelloController">

			

				<table id="data" style="height:250px">

					

					<tr>

						<td> <a class="group1" href="index.html">Student Name </a></td>

						<td> :</td>

						<td> <input type="text" name="studentname" id="studentname" class="studentname" ng-model="formAdata.txtname"></td>

						

						

					</tr>

					<?php 

					if(isset($check))

					{

						echo $check;

					}

					?>

						<tr>

						<td> Gender </td>

						<td> :</td>

						<td> 

							<select name="gender" id="gender" ng-model="formAdata.txtgender">

								<option value="Male">MALE </option>

								<option value="Female">FEMALE </option>

							</select>

						</td>

						

					</tr>

					

					

					<tr>

						<td> Phone </td>

						<td> :</td>

						<td> <input type="tel" name="phone" id="phone" ng-model="formAdata.txtphone" ></td>

						

					</tr>

					

					<tr>

						<td> Image  </td>

						<td> :</td>

						<td> <!--<input type="file"  name="myfile" onchange="angular.element(this).scope().submit_form(this.files)" id="myfile" >-->

						<input type="file"  name="myfile[]" ng-files="getTheFiles($files)" id="myfile" multiple></td>

						<td><img ng-src="" /></td>

						

					</tr>

					

					<tr>

						<td> address </td>

						<td> :</td>

						<td> <input type="text" name="address" id="address" ng-model="formAdata.txtaddress"></td>

						

					</tr>

					

					

					

					<tr>

					<input type="hidden" value="1" name="id" id="id" />

						<td> <input type="submit" value="SAVE" name="save" id="save" class="save" ng-click="submit_form()" >{{status}}</td>

						

						

					</tr>	

				</table>

			</form>

				

				

			

		</center>

		

	</body>



	<script src="js/angular.min.js"></script>

	 <script> 

         var app = angular.module("myapp", []);

		 app.directive('ngFiles', ['$parse', function ($parse) {



            function fn_link(scope, element, attrs) {

                var onChange = $parse(attrs.ngFiles);

                element.on('change', function (event) {

                    onChange(scope, { $files: event.target.files });

                });

            };



            return {

                link: fn_link

            }

        } ]);

         app.controller("HelloController", ['$scope','$http',function($scope,$http) { 

            $scope.formAdata=[];

			 

			 var formdata = new FormData();

			 

			

             $scope.getTheFiles = function ($files) {

				 

				angular.forEach($files, function (value,key) {

					formdata.append('file[]',value);

				});

				

            }

			$scope.submit_form = function()

			{

				// var formdata = new FormData();
				//state
				 //$scope.formAdata.state_abb ="IN-GJ";
				// $scope.formAdata.state_name = "Gujarat";
				// $scope.formAdata.is_display = ' yes ';
				
				// formdata.append('stateName',$scope.formAdata.state_name);
				// formdata.append('stateAbb',$scope.formAdata.state_abb);
				// formdata.append('isDisplay',$scope.formAdata.is_display);
				
				//city
				// $scope.formAdata.city_name = " Baroda ";
				// $scope.formAdata.is_display = ' yes';
				 //$scope.formAdata.state_abb = 'IN-GJ';
				
				 //formdata.append('cityName',$scope.formAdata.city_name);
				 //formdata.append('stateAbb',$scope.formAdata.state_abb);
				 //formdata.append('isDisplay',$scope.formAdata.is_display);
				
				//branch
				// $scope.formAdata.branch_name = "abcc!cc&-_`#().\'11";
				// $scope.formAdata.branch_name = "abcd";
				// $scope.formAdata.address1 ="35,abc2";
				// $scope.formAdata.address2 = "sdgd2";
				// $scope.formAdata.pincode = 324692;
				// $scope.formAdata.is_display = 'yes';
				// $scope.formAdata.is_default = 'not';
				// $scope.formAdata.state_abb= 'IN-MP';
				// $scope.formAdata.city_id= 1;
				// $scope.formAdata.company_id= 15;
				
				// formdata.append('branchName',$scope.formAdata.branch_name);
				// formdata.append('address1',$scope.formAdata.address1 );
				// formdata.append('address2',$scope.formAdata.address2 );
				// formdata.append('pincode',$scope.formAdata.pincode);
				// formdata.append('isDisplay',$scope.formAdata.is_display);
				// formdata.append('isDefault',$scope.formAdata.is_default);
				// formdata.append('stateAbb',$scope.formAdata.state_abb);
				// formdata.append('cityId',$scope.formAdata.city_id);
				// formdata.append('companyId',$scope.formAdata.company_id);
				
				//company
				// $scope.formAdata.company_name = "reema2232323222222222222222222222222222222222222";
				// $scope.formAdata.company_name = " 1sh1l lbaaaaa-&_().\'aadks "; //0-9 not allow(error:allow)
												
				//productCategory
				//$scope.formAdata.productCatName = "abcd";
				// $scope.formAdata.product_cat_desc = "abcdddddcc";
				// $scope.formAdata.is_display = 'yes ';
				// $scope.formAdata.product_parent_cat_id = 1;
				
				// formdata.append('productCategoryName',$scope.formAdata.productCatName);
				// formdata.append('productCategoryDescription',$scope.formAdata.product_cat_desc);
				// formdata.append('isDisplay',$scope.formAdata.is_display);
				// formdata.append('productParentCategoryId',$scope.formAdata.product_parent_cat_id);
				
				//productGroup
				// $scope.formAdata.product_group_name = "a&b";
				// $scope.formAdata.product_group_desc = "abcdddd";
				// $scope.formAdata.is_display = 'yes ';
				// $scope.formAdata.product_group_parent_id = 0;
				
				// formdata.append('productGroupName',$scope.formAdata.product_group_name);
				// formdata.append('productGroupDescription',$scope.formAdata.product_group_desc);
				// formdata.append('isDisplay',$scope.formAdata.is_display);
				// formdata.append('productGroupParentId',$scope.formAdata.product_group_parent_id);
				
				//product
				
				
				//template
				// $scope.formAdata.template_name = " abcffd ";
				// $scope.formAdata.template_type = ' general ';
				// $scope.formAdata.template_body=' <b> hiee</b> ';
				// $scope.formAdata.company_id=' 14 ';
				
				// formdata.append('templateName',$scope.formAdata.template_name);
				// formdata.append('templateType',$scope.formAdata.template_type);
				// formdata.append('templateBody',$scope.formAdata.template_body);
				// formdata.append('companyId',$scope.formAdata.company_id);
				
				//invoice
				// $scope.formAdata.invoice_label = " abcdeee ";
				// $scope.formAdata.invoice_type = ' prefix ';
				// $scope.formAdata.start_at=' 1 ';
				// $scope.formAdata.end_at=' 10 ';
				// $scope.formAdata.company_id='14 ';
				
				//formdata.append('invoiceLabel',$scope.formAdata.invoice_label);
				// formdata.append('invoiceType',$scope.formAdata.invoice_type);
				// formdata.append('startAt',$scope.formAdata.start_at);
				// formdata.append('endAt',$scope.formAdata.end_at);
				// formdata.append('companyId',$scope.formAdata.company_id);
				
				//quotation
				// $scope.formAdata.quotation_label = " abcd ";
				// $scope.formAdata.quotation_type = ' prefix ';
				// $scope.formAdata.start_at=' 1 ';
				// $scope.formAdata.end_at=' 10 ';
				// $scope.formAdata.company_id='14 ';
				
				// formdata.append('quotationLabel',$scope.formAdata.quotation_label);
				// formdata.append('quotationType',$scope.formAdata.quotation_type);
				// formdata.append('startAt',$scope.formAdata.start_at);
				// formdata.append('endAt',$scope.formAdata.end_at);
				// formdata.append('companyId',$scope.formAdata.company_id);
				
				//ledger
					
				//client
				// $scope.formAdata.client_name = "abcc";
				// $scope.formAdata.company_name = "abcd";
				// $scope.formAdata.contact_no = "87654534546";
				// $scope.formAdata.work_no = "87654534546";
				// $scope.formAdata.email_id = "abcd";
				// $scope.formAdata.address1 ="35,abc2";
				// $scope.formAdata.address2 = "sdgd2";
				// $scope.formAdata.is_display = 'yes';
				// $scope.formAdata.state_abb= 'IN-AG';
				// $scope.formAdata.city_id= 1;
				
				// formdata.append('clientName',$scope.formAdata.client_name);
				// formdata.append('companyName',$scope.formAdata.company_name);
				// formdata.append('contactNo',$scope.formAdata.contact_no);
				// formdata.append('workNo',$scope.formAdata.work_no);
				// formdata.append('emailId',$scope.formAdata.email_id);
				// formdata.append('address1',$scope.formAdata.address1);
				// formdata.append('address2',$scope.formAdata.address2);
				// formdata.append('isDisplay',$scope.formAdata.is_display);
				// formdata.append('stateAbb',$scope.formAdata.state_abb);
				// formdata.append('cityId',$scope.formAdata.city_id);
				//user
				
				// special journal
				// $scope.user = [{"jfId":4,"data":[{"amount": 10 ,"amountType":" credit ","ledgerId":10},{"amount":2,"amountType":"credit","ledgerId":12},{"amount":12,"amountType":"debit","ledgerId":9}],"entryDate":"22-10-2015","companyId":15}];
				//formdata.append('jfId',547);
 				//formdata.append('companyId',14);
 
 				//formdata.append('entryDate','22-10-2016');
 				//var json=[{"amount": 100 ,"amountType":" debit ","ledgerId":3},{"amount":50,"amountType":"credit","ledgerId":4},{"amount":50,"amountType":"credit","ledgerId":5}];

 				  
  				//for(var i=0;i<json.length;i++){
   
  					//angular.forEach(json[i], function (value,key) {
   						
   					//formdata.append('data['+i+']['+key+']',value);
  					//});
    
  				//}
				
				// sale/purchase
				// $scope.user = [{"jfId":4,"data":[{"amount": 10 ,"amountType":" credit ","ledgerId":1},{"amount":2,"amountType":"credit","ledgerId":1},{"amount":12,"amountType":"debit","ledgerId":1}],"entryDate":"22-10-2015","companyId":14,
				// "inventory":[{"productId": 10 ,"discount":12,"discountType":"flat","price":1300,"qty":44},{"productId": 10 ,"discount":12,"discountType":"flat","price":1300,"qty":44}],"companyId":14,"transactionDate":"22-10-2015","billNumber":23}];
				
				//formdata.append('jfId',4);
 				//formdata.append('companyId',15);
				//formdata.append('entryDate','22-10-2015');
				//var json=[{"amount": 10 ,"amountType":" credit ","ledgerId":10},{"amount":2,"amountType":"credit","ledgerId":10},{"amount":12,"amountType":"debit","ledgerId":10}];
				
				//var inventory = [{"productId": 5 ,"discount":12,"discountType":"flat","price":1300,"qty":44},{"productId": 10 ,"discount":12,"discountType":"flat","price":1300,"qty":44}];
				//formdata.append('transactionDate','22-10-2015');
				//formdata.append('invoiceNumber',23);
				//formdata.append('billNumber','');
				
				//for(var i=0;i<json.length;i++){
   
  					//angular.forEach(json[i], function (value,key) {
   						
   					//formdata.append('data['+i+']['+key+']',value);
  					//});
    
  				//}
				//for(var i=0;i<inventory.length;i++){
   
  					//angular.forEach(inventory[i], function (value,key) {
   						
   					//formdata.append('inventory['+i+']['+key+']',value);
  					//});
    
  				//}

				//transaction
				// $scope.user = [{"inventory":[{"productId":7 ,"discount":12,"discountType":"flat","price":1300,"qty":44},{"productId": 7 ,"discount":12,"discountType":"flat","price":1300,"qty":44}],"companyId":14,"transactionDate":"22-10-2015"}];
				
				//Bill PDF generate & insert bill data
				//$scope.user = [{"billData":[{"companyId":14,"entryDate":"22-10-2015","contactNo":"  	87654534545","emailId":"reemapatel25@gmail.co.in","companyName":"siliconbrain","clientName":"abc","invoiceNumber":"INV/2016-12/54","address1":"sfja,sa","address2":"dfsd,ds","stateAbb":"IN-GJ","cityId":2,"inventory":[{"productId": 10 ,"discount":12,"discountType":"flat","price":1300,"qty":44},{"productId": 10 ,"discount":12,"discountType":"flat","price":1300,"qty":44}],"total":232,"tax":232,"grandTotal":232,"advance":232,"balance":232,"paymentMode":"cash","bankName":"abc","checkNumber":"abbb34eQ1G","remark":"adsfsf afasf"}]}];
				
				// formdata.append('jfId',4);
 				// formdata.append('companyId',15);
 
 				// formdata.append('entryDate','22-10-2015');
 				//var json=[{"amount": 10 ,"amountType":" credit ","ledgerId":10},{"amount":2,"amountType":"credit","ledgerId":12},{"amount":12,"amountType":"debit","ledgerId":9}];

 				  
  				//for(var i=0;i<json.length;i++){
   
  					//angular.forEach(json[i], function (value,key) {
   						
   					//formdata.append('data['+i+']['+key+']',value);
  					//});
    
  				//}
				$scope.user = [{"billData":[{"companyId":24,"entryDate":"12-01-2017","contactNo":"9875347543","workNo":"9875647344","isDisplay":"yes","emailId":"reemapatel25@gmail.co.in","companyName":"siliconbrain","clientName":"palak","invoiceNumber":"INV/2016-12/53","address1":"sfja,sa","address2":"dfsd,ds","stateAbb":"IN-AG","cityId":1,"total":100,"tax":10,"grandTotal":134,"advance":100,"balance":10,"paymentMode":"cash","bankName":"abc","checkNumber":"abbb34eQ1G","remark":"adsfsf afasf"}]}];
				
				$scope.inventory=[{"productId": 898 ,"discount":12,"discountType":"flat","price":1300,"qty":44},{"productId":899 ,"discount":12,"discountType":"flat","price":1300,"qty":40}];
				
				angular.forEach($scope.user[0]['billData'][0], function (input,key) {
					
					formdata.append(key,input);
				});
				for(var i=0;i<$scope.inventory.length;i++)
				{
					angular.forEach($scope.inventory[i], function (input,key) {
						
						formdata.append('inventory['+i+']['+key+']',input);
					});
				}	
				// var clientId=2;
				 //var productId = 556;
				// var productGrpId = 11;
				// var productCatId = 16;
				// var companyId=25;
				// var cityId = 1;
				 // var stateAbb = "IN-AG";
				 // var branchId = 6;
				// var id = 42;
				// var templateId=1;
				// var bankId=2;
				// var invoiceId=7;
				// var quotationId=1;
				// var ledgerGrpId=1;
				// var ledgerId=8;
				//var invocieId=10;

				//var url = "http://api.swaminarayancycles.com/users";
				//var url="http://api.swaminarayancycles.com/accounting/ledgers/"+ledgerId+"/transactions";
				//var url = "http://api.swaminarayancycles.com/accounting/bills";
				
				// var url = "http://api.swaminarayancycles.com/clients/"+clientId;
				// var url = "http://api.swaminarayancycles.com/clients";
				
				// var url = "http://api.swaminarayancycles.com/documents/bill";
				// var url="http://api.swaminarayancycles.com/products/inward"; 
				// var url="http://api.swaminarayancycles.com/products/outward";

				// var url="http://api.swaminarayancycles.com/accos";
				// var url="http://api.swaminarayancycles.com/accounting/journals/company/"+companyId;
				// var url="http://api.swaminarayancycles.com/accounting/journals/next";
				//var url="http://api.swaminarayancycles.com/accounting/journals";
				//var url="http://api.swaminarayancycles.com/accounting/ledgers/company/"+companyId;
				
				// var url="http://api.swaminarayancycles.com/accounting/ledgers/"+ledgerId;
				 //var url="http://api.swaminarayancycles.com/accounting/ledgers";
				
				// var url="http://api.swaminarayancycles.com/accounting/ledger-groups/"+ledgerGrpId;
				// var url="http://api.swaminarayancycles.com/accounting/ledger-groups";
				// SELECT max(invoice_id) invoice_id,invoice_label FROM `invoice_dtl` where company_id=1
				// var url="http://api.swaminarayancycles.com/settings/quotation-numbers/company/"+companyId+"/latest";
				//var url="http://api.swaminarayancycles.com/settings/quotation-numbers";
				// var url="http://api.swaminarayancycles.com/settings/quotation-numbers/company/"+companyId;
				// var url="http://api.swaminarayancycles.com/settings/invoice-numbers/company/"+companyId+"/latest";
				// var url="http://api.swaminarayancycles.com/settings/invoice-numbers/"+invocieId;
				// var url="http://api.swaminarayancycles.com/settings/invoice-numbers/company/"+companyId;
				// var url="http://api.swaminarayancycles.com/banks/"+bankId;
				// var url="http://api.swaminarayancycles.comhttp://api.swaminarayancycles.com/banks";
				// var url="http://api.swaminarayancycles.com/settings/templates/"+templateId;
				// var url="http://api.swaminarayancycles.com/settings/templates";
				// var url="http://api.swaminarayancycles.com/companies/"+companyId;
				// var url="http://api.swaminarayancycles.com/companies";	
				// var url="http://api.swaminarayancycles.com/branches";
				 // var url="http://api.swaminarayancycles.com/branches/"+branchId;
				 //var url="http://api.swaminarayancycles.com/branches/company/"+companyId;
				 // var url="http://api.swaminarayancycles.com/states/"+stateAbb;
				//var url="http://api.swaminarayancycles.com/states";
				// var url="http://api.swaminarayancycles.com/cities/state/"+stateAbb;
				 var url="http://api.swaminarayancycles.com/cities";
				 // var url="http://api.swaminarayancycles.com/cities/"+cityId;
				// var url="http://api.swaminarayancycles.com/product-categories/"+productCatId;
				// var url="http://api.swaminarayancycles.com/product-categories";
				// var url="http://api.swaminarayancycles.com/product-groups";
				// var url="http://api.swaminarayancycles.com/product-groups/"+productGrpId;
				// var url="http://api.swaminarayancycles.com/products/"+productId;
				//var url="http://api.swaminarayancycles.com/products";
				// var url="http://api.swaminarayancycles.com/products/company/"+companyId+"/branch/"+branchId;

				$http({

                        url: url,

                        // type:'patch',

						method: 'post',

						// method: 'get',

						// method: "PATCH",

						// method:'delete',

						processData: false,

		      // headers: {'Content-Type': undefined,'authenticationToken':'d29ac73b666a3be3fc463448fdc5d9fc','salesType':'retail_sales'},
			 headers: {'Content-Type': undefined,'authenticationToken':'d29ac73b666a3be3fc463448fdc5d9fc'},
			//headers: {'Content-Type': undefined,'ledgerGroup':[9,12,32,18]},
			//headers: {'Content-Type': undefined,'fromDate':'30-11-2016','toDate':'01-12-2016'}
			//headers: {'Content-Type': undefined}

                    // data:formdata						
			//data:$scope.user
                        

                    }).success(function(data, status, headers, config) {

						console.log(data);	//post	//get	//update //delete

						$scope.status = status;

                    }).error(function(data, status, headers, config) {

                   // console.log(data);   
			$scope.status = status;
//console.log(status);

   //console.log(config);          
       });

			}

			

         }]); 

		 

      </script> 

</html>





