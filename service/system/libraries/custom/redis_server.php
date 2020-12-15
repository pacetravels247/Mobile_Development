<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/**
 * Redis Server
 *
 * A CodeIgniter library to interact with Redis
 *
 * @package CodeIgniter
 * @category Libraries
 * @author Arjun
 * @version v0.1
 */
class Redis_server {
	private $_ci;
	/**
	 * Connection
	 *
	 * Socket handle to the Redis server
	 *
	 * @var handle
	 */
	private $_connection;
	private $_ttl;
	
	/**
	 * Constructor
	 */
	public function __construct($params = array()) {
		//debug(get_class_methods('Redis'));exit;
		$ci = & get_instance ();
		$ci->load->config ( 'redis' );
		
		// Check for the different styles of configs
		if (isset ( $params ['connection_group'] )) {
			// Specific connection group
			$config = $ci->config->item ( 'redis_' . $params ['connection_group'] );
		} elseif (is_array ( $ci->config->item ( 'redis_default' ) )) {
			// Default connection group
			$config = $ci->config->item ( 'redis_default' );
		} else {
			// Original config style
			$config = array (
					'host' => $ci->config->item ( 'redis_host' ),
					'port' => $ci->config->item ( 'redis_port' ),
					'password' => $ci->config->item ( 'redis_password' ) 
			);
		}
		$this->_ttl = intval ( $config ['ttl'] );
		$this->_connection = new Redis ();
		$this->_connection->connect ( $config ['host'], $config ['port'] );
		$this->_connection->config ( 'SET', 'save', '' );
		$this->_connection->config ( 'SET', 'maxmemory-policy', 'allkeys-lru' );
		
		// echo "Server is running: ".$this->_connection->ping();exit;
	}
	public function test_redis()
	{
		error_reporting(E_ALL);
		
		$keys = $this->_connection->keys("*");	
		debug($keys);exit;
		debug($this->_connection->info());exit;
		//$this->get_configuration();
		
	}
	/**
	 * Ping and check if connection is working
	 */
	public function ping() {
		$ping = $this->_connection->ping ();
		if (strcmp ( $ping, 'PING' ) == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * set the key-value pair with time in seconds after which it should expire
	 */
	public function store_string($key, $value) {
		$res = $this->_connection->setex ( $key, $this->_ttl, $value );
		return $res;
	}
	
	/**
	 * Store list in the system
	 *
	 * @param string $key        	
	 * @param string $value        	
	 */
	public function store_list($key, $value) {
		$res = $this->_connection->rpush ( $key, $value );
		$this->set_expiry ( $key );
		return $res;
	}
	
	/**
	 * Get the Key value
	 */
	public function read_string($key) {
		return $this->_connection->get ( $key );
	}
	
	/**
	 * Store list in the system
	 *
	 * @param string $key        	
	 * @param string $value        	
	 */
	public function read_list($key, $offset, $limit) {
		$key = explode ( DB_SAFE_SEPARATOR, $key );
		$access_key = $key [0];
		if ($offset == - 1 || $limit == - 1) {
			$offset = $key [1] - 1;
			$limit = $key [1] - 1;
		}
		return $this->_connection->lrange ( $access_key, $offset, $limit );
	}
	public function set_expiry($key) {
		$this->_connection->setTimeout ( $key, $this->_ttl );
	}
	public function get_configuration() {
		debug ( $this->info ( 'memory' ) );
	}
	/**
	 * Generates and Returns Cache key for redis server
	 * Enter description here ...
	 */
	public function generate_cache_key()
	{
		$cache_key = md5(rand().time().rand());
		return $cache_key;
	}
	/**
	 * Generates and Returns Cache key for redis server
	 * Enter description here ...
	 */
	public function extract_cache_key($cache_key)
	{
		$key = explode ( DB_SAFE_SEPARATOR, $cache_key );
		$key = $key[0];
		return $key;
	}
}
