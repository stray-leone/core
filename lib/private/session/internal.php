<?php
/**
 * Copyright (c) 2013 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OC\Session;

/**
 * Class Internal
 *
 * wrap php's internal session handling into the Session interface
 *
 * @package OC\Session
 */
class Internal extends Memory {
	public function __construct($name) {
		session_name($name);
		session_start();
		if (!isset($_SESSION)) {
			throw new \Exception('Failed to start session');
		}
		$this->data = $_SESSION;
		session_write_close();
	}

	public function __destruct() {
		$_SESSION = array_merge($_SESSION, $this->data);
		session_write_close();
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		@session_start();
		$_SESSION[$key] = $value;
		$this->data[$key] = $value;
	}

	/**
	 * @param string $key
	 */
	public function remove($key) {
		// also remove it from $_SESSION to prevent re-setting the old value during the merge
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
		parent::remove($key);
	}

	public function clear() {
		session_unset();
		@session_regenerate_id(true);
		@session_start();
		$this->data = $_SESSION = array();
	}
}
