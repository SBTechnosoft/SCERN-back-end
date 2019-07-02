##### Standard Company Object

            {
                "companyId": int,
                "companyName": string,
				"companyDisplayName": string,
				"websiteName": string,
                "address1": string,
                "address2": string,
				"emailId":string,
				"printType":Enum,
				"customerCare":"string",
                "city":  {
					... Standard City Object
				},
                "state": {
                    ... Standard State Object
                },
                "pincode": int,
				"pan": string,
				"tin": string,
				"vatNo": string,
				"sgst":string,
				"cgst":string,
				"cess":decimal,
				"serviceTaxNo": char,
				"logo": {
					... Standard Document Object
				},
				"basicCurrencySymbol":char,
				"formalName":varchar,
				"noOfDecimalPoints":int,
				"currencySymbol":int,
				"isDefault": Enum,
				"isDisplay": Enum,
				"smsUser"::string,
				"smsPassword"::string,
				"smsSid"::string,
				"emailUserid"::string,
				"emailPassword"::string,
				"emailPort"::string,
				"emailHost"::string,
				"emailSmtpsecure":string,
				"createdAt" timestamp,
				"updatedAt": datetime
            }
            
            
##### Standard Company Persistable Object

         {
            "companyName": string,
			"companyDisplayName": string,
			"websiteName": string,
			"address1": string,
			"address2": string,
			"emailId":string,
			"customerCare":"string",
			"cityId": int,
			"stateAbb": char,
			"pincode": char,
			"printType":Enum,
			"pan": string,
			"tin": string,
			"sgst":string,
			"cgst":string,
			"cess":decimal,
			"vatNo": string,
			"serviceTaxNo": string,
			"file[]":
			{
				...Standard Document Persistable Object
			}
			"basicCurrencySymbol":varchar,
			"formalName":varchar,
			"noOfDecimalPoints":int,
			"currencySymbol":int,
			"isDefault": Enum,
			"isDisplay": Enum,
			"smsUser"::string,
			"smsPassword"::string,
			"smsSid"::string,
			"emailUserid"::string,
			"emailPassword"::string,
			"emailPort"::string,
			"emailHost"::string,
			"emailSmtpsecure":string,
		}

##### Is Display Enum
		{
			... Is Display Enum
		}

##### Is Default Enum
		{
			... Is Default Enum
		}
##### Print Enum
		{
			"print":'print',
			"preprint":'preprint'
		}
