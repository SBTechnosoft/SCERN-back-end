##### Creates Expense

##### `POST /settings/expenses/`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Expense Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }
    
##### Gets Expense           
            
##### `GET /settings/expenses/{expenseId}`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Expense Object
			}

**NOTES:** List all the Expense as per given expense_id 

##### `GET /settings/expenses`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Expense Object
			}

**NOTES:** List all the expenses 
##### Updates Expense    
       
##### `UPDATE /settings/expenses/{ExpenseId}`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Expense Persistable Object
            }       
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }

##### `DELETE /settings/expenses/{ExpenseId}`
+ Header
	- Authentication Token
     
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }