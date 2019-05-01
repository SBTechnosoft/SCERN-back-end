##### Company Validation Rules

- company_name
	`between:1,35|regex:/^[a-zA-Z &_`#().\'-]+$/` 
			
- company_display_name
	`between:1,50|regex:/^[a-zA-Z &_`#().\'-]+$/`
	
- address1
	`between:1,35|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/`
	
- address2
	`between:1,35|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/`
	
- pincode
	`between:6,10|regex:/^[0-9]+$/`
	
- pan
	`max:10|min:10|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/`
	
- tin
	`max:11|min:11|regex:/^([a-zA-Z0-9])+$/`
	
- vat_no
	`max:11|min:11|regex:/^([a-zA-Z0-9])+$/`
	
- service_tax_no
	`between:1,35|regex:/^([a-zA-Z0-9]{15})+$/`
	
- basic_currency_symbol
	`max:3|min:3`
	
- formal_name
	`between:1,35|regex:/^[a-zA-Z &_`#().\'-]+$/`
