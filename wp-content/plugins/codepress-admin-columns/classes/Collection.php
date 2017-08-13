<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AC_Collection
 *
 * Used to hold values from the same type
 */
class AC_Collection
	implements Iterator {

	/**
	 * @var array
	 */
	protected $items;

	public function __construct( array $items = array() ) {
		$this->items = $items;
	}

	public function all() {
		return $this->items;
	}

	public function has( $key ) {
		return isset( $this->items[ $key ] );
	}

	public function put( $key, $value ) {
		$this->items[ $key ] = $value;

		return $this;
	}

	public function push( $value ) {
		$this->items[] = $value;
	}

	public function get( $key, $default = null ) {
		if ( $this->has( $key ) ) {
			return $this->items[ $key ];
		}

		return $default;
	}

	public function __get( $key ) {
		return $this->get( $key );
	}

	public function rewind() {
		reset( $this->items );
	}

	public function current() {
		return current( $this->items );
	}

	public function key() {
		return key( $this->items );
	}

	public function next() {
		return next( $this->items );
	}

	public function valid() {
		$key = $this->key();

		return ( $key !== null && $key !== false );
	}

	/**
	 * Filter collection items
	 *
	 * @return AC_Collection
	 */
	public function filter() {
		return new AC_Collection( ac_helper()->array->filter( $this->items ) );
	}

	/**
	 * @param string $glue
	 *
	 * @return string
	 */
	public function implode( $glue = '' ) {
		return implode( $glue, $this->items );
	}

}
