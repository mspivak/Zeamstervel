<?php namespace Mauro\Zeamstervel;

use Eloquent;
use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Contracts\JsonableInterface;

abstract class Model implements ArrayAccess, JsonSerializable, JsonableInterface {

	protected $attributes;
	protected $original;

	protected $_httpClient;

	protected $_endpoint;

	protected $appends = ['location_id'];

	function __construct( array $attributes = array() ) {
		$this->fill( $attributes );
	}

	public function save() {

		if ( isset($this->{$this->getPrimaryKey()}) ) {
			return $this->update();
		}
		return $this->store();

	}

	public function find( $id ) {

		$response = $this->client()->get( $this->getEndpoint(), $id );
		$this->fill($response);
		$this->syncOriginal();
		return $this;

	}

	public function store() {

		$response = $this->client()->post( $this->getEndpoint(), $this );
		$this->fill( $response );
		$this->syncOriginal();
		return $this;

	}

	public function update() {

		$response = $this->client()->put( $this->getEndpoint().'/'.$this->id, [$this->getName() => $this->getDirty()] );
		$this->fill( $response );
		$this->syncOriginal();
		return $this;

	}

	protected function getLocationIdAttribute() {
		return $this->client()->location_id;
	}


	//@TODO: use a facade for this:
	protected function client() {
		if (is_null($this->_httpClient)) {
			$this->_httpClient = new Client();
		}
		return $this->_httpClient;
	}


	public function getPrimaryKey() {
		if ( $this->primary_key ) {
			return $this->primary_key;
		}
		return 'id';
	}

	public function getName() {
		return strtolower(camel_case(class_basename(get_class($this))));
	}

	public function getEndpoint() {
		return strtolower( str_plural( $this->getName() ) );
	}

	public function toJson( $options = JSON_FORCE_OBJECT ) {
		return json_encode([ $this->getName() => $this->getAttributes() ], $options);
	}

	public function __toString() {
		return $this->toJson();
	}

	public function jsonSerialize() {
		return (string) $this;
	}

	public function syncOriginal() {
		$this->original = $this->attributes;
		return $this;
	}

	public function getDirty() {
		$dirty = [];

		foreach ($this->attributes as $key => $value) {
			if ( !array_key_exists($key, $this->original) 
			||	 $value !== $this->original[$key]) {
				$dirty[$key] = $value;
			}
		}

		return $dirty;

	}

	public function fill( array $attributes ) {
		foreach ( $this->fillable as $key) {
			if (array_key_exists($key, $attributes))
				$this->{$key} = $attributes[$key];
		}
		return $this;
	}

	public function __get($key) {
		return $this->getAttribute($key);
	}

	public function __set($key, $value) {
		$this->setAttribute($key, $value);
	}

	public function __isset($key) {
		return (isset($this->attributes[$key]) ||
				($this->hasGetMutator($key) && ! is_null($this->getAttribute($key))));
	}

	public function __unset($key) {
		unset($this->attributes[$key]);
	}

	public function getAttribute($key) {

		if (array_key_exists($key, $this->attributes))
			return $this->attributes[$key];
		elseif ( $this->hasGetMutator($key) ) 
			return $this->mutateAttribute($key, $this->getAttributeFromArray($key));

	}

	public function getAttributes() {

		$attributes = $this->attributes;

		foreach ($this->appends as $key) {
			$attributes[$key] = $this->mutateAttribute($key);
		}

		return $attributes;

	}

	protected function getAttributeFromArray($key){
		if (array_key_exists($key, $this->attributes)) {
			return $this->attributes[$key];
		}
	}

	protected function hasGetMutator($key) {
		return method_exists($this, 'get'.studly_case($key).'Attribute');
	}

	protected function mutateAttribute($key, $value = null) {
		return $this->{'get'.studly_case($key).'Attribute'}($value);
	}

	public function setAttribute($key, $value)
	{
		if ($this->hasSetMutator($key)) {
			$method = 'set'.studly_case($key).'Attribute';
			return $this->{$method}($value);
		}

		// elseif (in_array($key, $this->getDates()) && $value) {
		// 	$value = $this->fromDateTime($value);
		// }

		$this->attributes[$key] = $value;
	}

	public function hasSetMutator($key) {
		return method_exists($this, 'set'.studly_case($key).'Attribute');
	}

	public function offsetExists($offset) {
		return isset($this->$offset);
	}

	public function offsetGet($offset) {
		return $this->$offset;
	}

	public function offsetSet($offset, $value) {
		$this->$offset = $value;
	}

	public function offsetUnset($offset) {
		unset($this->$offset);
	}

	public function toArray() {
		return $this->getAttributes();
	}

}