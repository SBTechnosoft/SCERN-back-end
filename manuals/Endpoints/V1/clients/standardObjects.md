##### Standard Client Object

            {
                "clientId": int,
                "clientName": string,
				"contactNo": string,
				"contactNo1": string,
				"emailId": string,
				"address1": string,
				"gst": string,
				"isDisplay": Enum,
				"birthDate":date,
				"anniversaryDate":date,
				"otherDate":date,
				"createdAt": timestamp,
				"updatedAt": datetime,
				"closingBalance:
				[
					... Standard Closing Balance Object
				]
				"profession":
				{
					... Standard Profession Object
				}
				"document":
				{
					... Standard Document Object
				}
				"city": 
				{
					... Standard City Object
				}
				"state": 
				{
					... Standard State Object
				}
                
            }

##### Standard Client Persistable Object

 			{
            	"clientName": string,
				"contactNo": string,
				"contactNo1": string,
				"emailId": string,
				"address1": string,
				"gst": string,
				"professionId":int,
				"birthDate":date,
				"anniversaryDate":date,
				"otherDate":date,
				"isDisplay": Enum,
				"cityId":int, 
				"stateAbb":char
			}
			
#####  Is Display Enum
			{
				... Is Display Enum
			}
##### Standard Closing Balance Object
			{
				clientId: int,
				closingBalance: decimal,
				closingBalanceType: 'debit',
				companyId: int,
				ledgerId: int,
				ledgerName: string,
				outstandingLimit: decimal,
				outstandingLimitType: string
			}