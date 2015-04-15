<?php namespace Mauro\Zeamstervel;

use Config;

class Zeamstervel {

	// protected $_httpClient;

	static function contact( array $attributes = array() ) {

		return new Contact( $attributes );	

	}

	static function accountVault( array $attributes = array() ) {

		return new AccountVault( $attributes );

	}

	static function transactionWithTicket( $ticket, $attributes ) {

		$attributes['ticket'] = $ticket;
		$attributes['payment_method'] = 'cc';
		$attributes['action'] = 'sale';

		return new Transaction( $attributes );

	}

	// public static function client() {
	// 	if ($this->_httpClient == null) {
	// 		$this->_httpClient = new Client;
	// 	}
	// 	return $_httpClient;
	// }

}