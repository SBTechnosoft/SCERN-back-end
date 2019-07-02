##### Standard QuotationNumber Object
			{
                "QuotationId": int,
                "company":  {
					... Standard Company Object
				},
                "QuotationLabel": string,
                "QuotationType": enum,
                "createdAt": timestamp,
				"updatedAt":datetime
			}
            
##### Standard QuotationNumber Persistable Object
			{
            	"companyId":int, 
                "QuotationLabel": string,
                "QuotationType": enum,
            }

##### Quotation Type Enum
			{
				beforeQuotation:'prefix',
				afterQuotation:'postfix'
			}

