##### Standard Branch Object

            {
                "branchId": int,
                "company":  {
					... Standard Company Object
				},
                "branchName": string,
                "address1": string,
                "address2": string,
                "city":  {
					... Standard City Object
				},
				"state": {
					... Standard State Object
				},
				
				"pincode": char,
				"isDefault":Enum,
				"isDisplay": Enum,
				"createdAt": timestamp,
				"updatedAt": datetime
            }
            
##### Standard Branch Persistable Object
			{
            	"companyId":int, 
                "branchName": string,
                "address1": string,
                "address2": string,
                "cityId": int,
				"stateAbb": char,
				"pincode": char,
				"isDefault":Enum,
				"isDisplay": Enum,
			}

#####  Is Display Enum
			{
				... Is Display Enum
			}
			
#####  Is Default Enum
			{
				... Is Default Enum
			}