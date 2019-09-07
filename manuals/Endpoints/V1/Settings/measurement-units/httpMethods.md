##### Get Measurement Units

##### `GET /settings/measurement-units`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}            
+ Response

            [
                ... Standard Measurement Unit Object
                ... 
            ]


##### `GET /settings/measurement-units/{measurementUnitId}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Measurement Unit Object
            }

##### `POST /settings/measurement-units`
+ Header
	- Authentication Token

+ Body

			{
				... Standard Persistable Measurement Unit Object
			}

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP Status 200
            }

##### `POST /settings/measurement-units/{measurementUnitId}`
+ Header
	-	 Authentication Token

+ Body

			{
				... Standard Persistable Measurement Unit Object
			}

+ Error Message

			{
				... Error Message
			}

+ Response

			{
				... HTTP Status 200
			}
