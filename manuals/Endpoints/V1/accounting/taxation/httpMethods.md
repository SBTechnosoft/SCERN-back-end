##### Creates Taxation for tax sales

##### `GET /accounting/taxation/sale-tax/company/{companyId}`
+ Header
	- Authentication Token
	- 'operation':'excel',
	- 'fromDate':'',
	- 'toDate':''
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Sale-Tax Object
            }

##### Creates Taxation for tax purchase

##### `GET /accounting/taxation/purchase-tax/company/{companyId}`
+ Header
	- Authentication Token
	- 'operation':'excel'
	- 'fromDate':'',
	- 'toDate':''
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Purchase-Tax Object
            }

			
##### Creates Taxation for purchase-detail

##### `GET /accounting/taxation/purchase-detail/company/{companyId}`
+ Header
	- Authentication Token
	- 'operation':'excel'
	- 'fromDate':'',
	- 'toDate':''
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Purchase-Detail Object
            }
##### Creates Taxation of GST-Return

##### `GET /accounting/taxation/gst-return/company/{companyId}`
+ Header
	- Authentication Token
	- 'operation':'gst-return-excel'
	- 'fromDate':'',
	- 'toDate':''
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Document Path
            }
			
##### Creates product-stock

##### `GET /accounting/taxation/stock-detail/company/{companyId}`
+ Header
	- Authentication Token
	- 'fromDate':'',
	- 'toDate':''
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Stock-Detail Object
            }

##### Creates income-expense

##### `GET /accounting/taxation/income-expense/company/{companyId}`
+ Header
	- Authentication Token
	- 'fromDate':'',
	- 'toDate':''
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Income-Expense Object
            }

##### Creates Taxation of GSTR2

##### `GET /accounting/taxation/gstr2/company/{companyId}`
+ Header
	- Authentication Token
	- 'operation':'gst-r2-excel'
	- 'fromDate':'',
	- 'toDate':''
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Document Path
            }

##### Creates Taxation of GSTR3

##### `GET /accounting/taxation/gstr3/company/{companyId}`
+ Header
	- Authentication Token
	- 'operation':'gst-r3-excel'
	- 'fromDate':'',
	- 'toDate':''
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Document Path
            }
			