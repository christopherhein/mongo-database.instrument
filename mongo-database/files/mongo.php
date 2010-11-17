<?php
/**
* MongoDB Connection File
*
* Easy to use MongoDB Connection and ORM
*
* Licensed under the MIT license.
*
* @category   Orchestra
* @copyright  Copyright (c) 2010, Christopher Hein
* @license    http://orchestramvc.chrishe.in/license
* @version    Release: 0.0.1:beta
* @link       http://orchestramvc.chrishe.in/docs/lib/database/mongodb/
*
*/
require(ROOT.'/config/config.php');
require(ROOT.'/config/database.php');

class Database {
	protected $_connection;
  protected $_handle;
  protected $_type;
  protected $_server;
	protected $_port;
	protected $_name;
  
  public function __construct() {
    global $app;
    global $db;
    
    $this->_type    = $db[$app['status']]['type'];
    $this->_server  = $db[$app['status']]['server'];
    $this->_port  	= $db[$app['status']]['port'];
    $this->_name  	= $db[$app['status']]['name'];
  }
  
  private function connect($cmd = true) {
		if(!$cmd) {
	    $this->_connection = new Mongo("'".$this->_server.":".$this->_port."'");
		} else {
			$this->_connection = new Mongo();
		}
		$this->_handle = $this->_connection->selectDB("$this->_name");
  }

  private function disconnect() {
    $this->_connection->close;
  }
  
	public function select($collect, $fields = array()) {
		if($collect != NULL) {
			$this->connect();
			$collection = $this->_handle->selectCollection("$collect");
			return $collection->find($fields);
			$this->disconnect();
		}
	}
	
  public function create($collect, $fields) {
		if($collect || $fields != NULL) {
			unset($fields['submit']);
			$fields['created_at'] = time();
			$fields['updated_at'] = time();
			$this->connect();
			$collection = $this->_handle->selectCollection("$collect");
			$collection->insert($fields);
			$this->disconnect();
		}
  }
  
  public function update($collect, $criteria, $fields, $options) {
		if($collect || $fields != NULL) {
			//$id = $fields['id'];
			unset($fields['id']);
			unset($fields['submit']);
			$this->connect();
			$collection = $this->_handle->selectCollection("$collect");
			$select = $this->select($collect, $criteria);
			foreach($select as $update) {
				while(list($k, $v) = each($fields)) {
					$update["$k"] = $v;
				}
				$update['updated_at'] = time();
			}
			$collection->update($criteria, $update, $options);
			$this->disconnect();
		}
  }
	
	function login($collect, $fields = array()) {
		$this->connect();
		$collection = $this->_handle->selectCollection("$colect");
		$select = $this->select();
		// if($run) {
		// 	$user = mysql_fetch_assoc($run);
		// 	return $user['id'];
		// } else {
		// 	return false;
		// }
		$this->disconnect();
	}

  public function destroy($collect, $fields = array()) {
		if($collect || $fields != NULL) {
  		$this->connect();
			$collection = $this->_handle->selectCollection("$collect");
			$collection->remove($fields);
			$this->disconnect();
		} 
  }

}