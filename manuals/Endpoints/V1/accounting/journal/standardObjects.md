##### Standard JournalFolioId Object
			{
            	"jfId":int,
            }
			
##### Standard PaymentReceipt Object
			{
				"journalId":int,
            	"jfId":int,
				"creditAmount":decimal,
				"creditArray": ... Standard Journal Object,
				"debitAmount":decimal,
				"debitArray": ... Standard Journal Object,
				"amountType":Enum,
				"entryDate":DateTime,
				"createdAt":TimeStamp,
				"updatedAt":DateTime,
				"ledger":{
					... Standard Ledger Object
				}
            }
			

##### Standard Journal Object
			{
				"journalId":int,
            	"jfId":int,
				"amount":decimal,
				"amountType":Enum,
				"entryDate":DateTime,
				"createdAt":TimeStamp,
				"updatedAt":DateTime,
				"ledger":{
					... Standard Ledger Object
				}
				"company": {
					... Standard Company Object
				}
            }
##### Standard Journals Persistable Object
			{
            	"data":
				[
					{
						"amount":decimal,
						"amountType":Enum,
						"ledgerId":int,	
					},
					...
				]
				"entryDate":Date,
                "companyId":int
			}

#### Standard Journals Purchase Persistable Object
			{
            	"data":
				[
					{
						"amount":decimal,
						"amountType":Enum,
						"ledgerId":int,	
					},
					...
				]
				"entryDate":Date,
                "companyId":int,
				"clientName":string
			}
			
##### Standard Inventory Persistable Object
			{
				... Standard Product Transaction Persistable Object,
				"invoiceNumber":String
			}
##### Amount Type Enum
			{
				creditType:'credit',
				debitType:'debit'
			}