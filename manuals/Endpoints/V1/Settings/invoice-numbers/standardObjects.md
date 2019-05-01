##### Standard Invoice Number Object
			{
                "invocieId": int,
                "company":  {
					... Standard Company Object
				},
                "invoiceLabel": string,
                "invoiceType": Enum,
                "createdAt" : timestamp,
				"updatedAt": datetime
			}
            
##### Standard Invoice Number Persistable Object
			{
            	"companyId":int, 
                "invoiceLabel": string,
                "invoiceType": Enum,
            }

##### Invoice Type Enum
			{
				beforeInvoice:'prefix',
				afterInvoice:'postfix' 
			}

