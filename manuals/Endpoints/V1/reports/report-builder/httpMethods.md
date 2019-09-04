##### Creates Bill

##### `GET /reports/report-builder/groups/`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}            
+ Response

            [
				... Standard ReportGroup Object,
				...
			]

##### `POST /reports/report-builder/preview`
+ Header
	- Authentication Token

+ Body

			{
				"headers": ... Stringified Preview Header Object
				"columns": ... Stringified Preview Column Object,
				"groupBy": ... Stringified Preview Column Object,
				"orderBy": ... Stringified Preview Column Object,
				"filters": [
					... Stringified Standard Filter Object,
					...
				]
			}

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Table Object
            }
			
#### `POST /reports/report-builder`
+ Header
	-	 Authentication Token

+ Body

			{
				"headers": ... Stringified Standard Header Object,
				"columns": ... Stringified Standard Column Object,
				"groupBy": ... Stringified Preview Column Object,
				"orderBy": ... Stringified Preview Column Object,
				"filters": [
					... Stringified Standard Filter Object,
					...
				]
			}
+ Error Message
 
			{
				... Error Message
			}
+ Response

			{
				... HTTP Status 200
			}

#### `POST /reports/report-builder/{reportId}`
+ Header
	-	Authentication Token

+ Body

		{
			"headers": ... Stringified Standard Header Object,
			"columns": ... Stringified Standard Column Object,
			"groupBy": ... Stringified Preview Column Object,
			"orderBy": ... Stringified Preview Column Object,
			"filters": [
				... Stringified Standard Filter Object,
				...
			]
		}
+ Error Message

		{
			... Error Message
		}

+ Response

		{
			... HTTP Status 200
		}

#### `GET /reports/report-builder`
+ Header
	- Authentication Token

+ Error Message

		{
			... Error Message
		}

+ Response

		[
			... Minified Report Object,
			...
		]

#### `GET /reports/report-builder/{reportId}`
+ Header
	- Authentication Token

+ Error Message

		{
			... Error Message
		}
+ Response

		{
			"fields": [
				... Standard Column Object,
				...
			],
			"filters": [
				... Preview Filter Object,
				...
			],
			"headers": {
				"groupBy": ... Standard Column Object,
				"orderBy": ... Standard Column Object,
				... Standard Header Object
			}
		}

#### `GET /reports/report-builder/generate/{reportId}`

+ Header
	- Authentication Token

+ Error Message

		{
			... Error Message
		}

+ Response

		{
			"fields": [
				{
					"id": Int,
					"label": String
				},
				...
			],
			"data": [
				{
					"fieldname": String,
					...
				}
			]