<?php namespace Mauro\Zeamstervel;

use Zeamster;

class Contact extends Model {
	
	protected $fillable = [	'id', 'contact_api_id', 'first_name', 'last_name', 'email', 'address', 'city', 'zip', 
							'home_phone', 'cell_phone', 'office_phone', 'office_ext_phone', 'date_of_birth'];



	public function addAccountVault( array $attributes = array() ) {

		$attributes['contact_id'] = $this->id;

		return Zeamster::accountVault( $attributes );

	}

}