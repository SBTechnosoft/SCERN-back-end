##### Standard Expense Object

            {
                "expenseId": int,
                "expenseName":String,
                "expenseType":Enum,
                "expenseValue":decimal,
                "company":{
                    ... Standard Company Object
                },
				"createdAt":TimeStamp,
                "updatedAt":datetime,
				"deletedAt":datetime,
			}
            
##### Standard Expense Persistable Object
			{
            	"expenseId": int,
                "expenseName":String,
                "expenseType":Enum,
                "expenseValue":decimal,
                "companyId":int
            }
#####  Expense Type Enum
            {
                #####  Discount Type Enum(product)
            }
