##### Standard User Object

            {
                "userId": int,
                "userName": string,
				"emailId":string,
				"userType":Enum,
				"password":string,
				"contactNo":string,
				"address":string,
				"pincode":int,
				"permissionArray":string,
				"state": {
                    ... Standard State Object
				},
				"city": {
                    ... Standard City Object
				},
				"company": {
                    ... Standard Company Object
				},
				"branch": {
                    ... Standard Branch Object
				},
				"createdAt" timestamp,
				"updatedAt": datetime
            }
            
            
##### Standard user Persistable Object

 			{
            	"userName": string,
				"userType":Enum,
				"emailId":string,
				"password":string,
				"contactNo":string,
				"address":string,
				"pincode":int,
				"permissionArray":string,
				"stateAbb":char,
				"cityId": int,
				"companyId":int,
				"branchId": int,
			}
##### user type enum
			{
				adminType :'admin',
				staffType :'staff'
			}
