<?php namespace Mauro\Zeamstervel;

class AccountVault extends Model {
	
	protected $fillable = [ 'id','title','account_holder_name','first_six','last_four','billing_address','billing_zip',
							'card_type','exp_date','routing','account_type','created_ts','modified_ts','account_vault_api_id',
							'contact_id','location_id','expiring_in_months','has_recurring', 'ticket', 'payment_method' ];



}