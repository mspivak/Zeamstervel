<?php namespace Mauro\Zeamstervel;

use GuzzleHttp\Exception\ClientException;

class Exception extends \Exception {

    protected $statusCode = 400;
    
    protected $details = [
        'message' => 'The transaction was declined without further explanation.'
    ];

    public function getDetails() {

        if ( !is_null($this->getPrevious())
        &&   $this->getPrevious() instanceof ClientException ) {
            return json_decode( $this->getPrevious()->getResponse()->getBody() )->errors;
        }

        return $this->details;
    }

}