##### Standard Cash-Flow Object

            {
                "cashFlowId":int,
				"ledger":
				{
					... Standard Ledger Object
				},
				"amount":decimal,
				"amountType":Enum,
				"entryDate":Date,
				"createdAt":TimeStamp,
                "updatedAt":DateTime,
                "company": {
					... Standard Company Object
				}
			}
##### Amount Type Enum
			{
				... Amount Type Enum(journal)
			}