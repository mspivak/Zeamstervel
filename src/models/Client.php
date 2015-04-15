<?php namespace Mauro\Zeamstervel;

use StdClass;
use Config;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;

class Client {

	protected $url;	

	protected $_httpClient;

	public $location_id;

	function __construct() {

		// $this->url = 'https://'.Config::get('zeamstervel::host').'/'.Config::get('zeamstervel::namespace').'/';

		$this->location_id = Config::get('zeamstervel::location-id');

		$this->setClient( new Guzzle( [
			'scheme' => 'https',
			'base_url' => [ 'https://'.Config::get('zeamstervel::host').'/{namespace}/', ['namespace' => Config::get('zeamstervel::namespace')] ],
			'defaults' => [
				'headers' => [
					'Content-Type' => 'application/json',
					'user-id' => Config::get('zeamstervel::user-id'),
					'user-api-key' => Config::get('zeamstervel::user-api-key'),
					'developer-id' => Config::get('zeamstervel::developer-id'),
				],
				'query' => [
					'location_id' => Config::get('zeamstervel::location-id'),
				]
			],
		] ) );

		//dd($this->getClient());

	}

	function setClient( Guzzle $client ) {
		$this->_httpClient = $client;
	}

	function getClient() {
		return $this->_httpClient;
	}

	public function get( $endpoint, $id = null ) {

		if (!is_null($id))
			$endpoint .= '/'.$id;

		$response = $this->request('GET', $endpoint)->json();

		return array_shift($response);

	}

	public function post( $endpoint, $data ) {

		$response = $this->request('POST', $endpoint, ['body' => $data->toJson() ] )->json();
		return array_shift($response);

	}

	public function put( $endpoint, $data ) {

		$response = $this->request('PUT', $endpoint, ['body' => json_encode($data) ] );
		return array_shift($response);

	}

	protected function request($method, $endpoint, $options = []) {

		$method = strtolower($method);

		try {

			return $this->getClient()->$method($endpoint, $options);

		} catch ( ClientException $e ) {
			
			throw new Exception( 400, $e->getResponse()->getReasonPhrase(), $e );

		}

	}

}