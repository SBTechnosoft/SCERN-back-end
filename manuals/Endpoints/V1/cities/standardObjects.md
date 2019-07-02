##### Standard City Object

            {
                "cityId": int,
                "cityName": string,
				"state": {
                    ... Standard State Object
				},
				"isDisplay": Enum,
				"createdAt" timestamp,
				"updatedAt": datetime
                
            }
            
            
##### Standard city Persistable Object

 			{
            	"cityName": string,
            	"stateAbb": char,
				"isDisplay": Enum,
			}
##### Is Display Enum
			{
				... Is Display Enum
			}
