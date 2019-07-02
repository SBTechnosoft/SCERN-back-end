##### Standard Job-Form-Number Object

            {
				"jobCardNumberId":int,
				"jobCardNumberLabel":string,
				"jobCardNumberType": enum,
				"startAt": int,
				"endAt":int,
				"company":  {
					... Standard Company Object
				},
				"createdAt" timestamp,
				"updatedAt": datetime
            }
            
            
##### Standard Job-Form-Number Persistable Object

         {
			"jobCardNumberLabel":string,
            "jobCardNumberType": enum,
			"startAt": int,
			"endAt":int,
			"companyId": int
		}
##### Job-Form-Number Type Enum
			{
				beforeJobFormNumber:'prefix',
				afterJobFormNumber:'postfix' 
			}
