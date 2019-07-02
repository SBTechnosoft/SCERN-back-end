##### Branch Validation Rules

- branch_name
	`between:1,35|regex:/^[a-zA-Z &_`#().\'-]*$/`
 
- address1
	`between:1,35|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/`
			
- address2 
	`between:1,35|regex:/^[a-zA-Z0-9 *-\/_`#\[\]().\']+$/`

- pincode
	`between:6,10|regex:/^[0-9]+$/`
