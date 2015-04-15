<?php namespace Mauro\Zeamstervel;

use Zeamster;

class Transaction extends Model {
	
	protected $fillable = ['id', 'amount', 'verviage', 'contact_id', 'ticket', 'payment_method', 'action', 'transaction_amount', 'status_id', 'type_id', 'verbiage'];

	public function process() {

		$response = $this->store();

		if ( !$this->is_successful() ) {

			throw new Exception( $response->verbiage );

		}

		return $response;

	}

	public function is_successful() {
		return (bool) ($this->status_id == 101);
	}


}