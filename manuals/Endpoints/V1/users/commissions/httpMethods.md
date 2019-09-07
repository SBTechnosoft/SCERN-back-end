##### Store User's Commission

##### `POST /users/commission/{userId}`
+ Header
	- 'authenticationToken':string

+ Body

			{
				... Standard Commission Persistable Object
			}

+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... HTTP Status 200
			}

##### `GET /users/commission/{userId}`

+ Header
	- 'authenticationToken': String
+ Error Message

			{
				... Error Message
			}

+ Response

			{
				... Standard Commission Object
			}

##### `GET /users/commission/item-wise`

+ Header
	- 'authenticationToken': String
	- 'companyId': Int
+ Error Message

			{
				... Error Message
			}

+ Response

			[
				... Standard ItemWise Commission Object,
				...
			]


##### `POST /users/commission/item-wise`

+ Header
	- 'authenticationToken': String,
+ Body
	
			{
				... Itemwise Commission Persistable Object
			}

+ Error Message
		
			{
				... Error Message
			}

+ Response

			{
				... HTTP Status 200
			}


##### `POST /users/commission/item-wise/{productCommissionId}`

+ Header
	- 'authenticationToken': String
+ Body

			{
				... Itemwise Commission Persistable Object
			}

+ Error Message

			{
				... Error Message
			}

+ Response

			{
				... HTTP Status 200
			}

##### `GET /users/commissions/report/{userId}`

+ Header
	- 'authenticationToken': String
	- 'companyId': Int
+ Error Message

			{
				... Error Message
			}

+ Response

			[
				
			]