##### Creates Authentication Token

##### `POST /authenticate`
+ Header


+ Body
			{
                ... Standard Authentication Persistable Object
            }
+ Error Message

			{
				... Error Message
			}               
+ Response

            {
                "authenticationToken":string
			}

##### `GET /authenticate`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}               
+ Response

            {
                ... Standard User Object
			}
			
##### `GET /authenticate/users/{userId}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}               
+ Response

            {
                ... Standard User Object
			}