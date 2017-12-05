<?php

namespace Harmony2\Http;

// PHPCORE
use InvalidArgumentException;
use DateTime;
use Harmony2\Router\Route;

/**
 * Gestion de la requête envoyée au serveur
 */
class Request
{

	/**
	 * @var \Harmony2\Router\Route
	 */
	private $route;

	/** @var array $_server */
	private $_server;

	/** @var array $_post */
	private $_post = [];

	/** @var array $_get */
	private $_get = [];

	/** @var array $_request */
	private $_request = [];

	/** @var array $_files */
	private $_files = [];

	/** @var array $_cookie */
	private $_cookie = [];

	/** @var array $_session */
	private $_session = [];

	/** @var boolean $_hasserver */
	private $_hasserver = false;

	/** @var boolean $_haspost */
	private $_haspost = false;

	/** @var boolean $_hasget */
	private $_hasget = false;

	/** @var boolean $_hasrequest */
	private $_hasrequest = false;

	/** @var boolean $_hasfiles */
	private $_hasfiles = false;

	/** @var boolean $_hascookie */
	private $_hascookie = false;

	/** @var boolean $_hassession */
	private $_hassession = false;


	public function setSessionTable(array $session)
	{
		$this->_session = $session;
		$this->_hassession = true;
	}

	public function setFilesTable(array $files)
	{
		$this->_files = $files;
		$this->_hasfiles = true;
	}

	public function getFilesTable(){
		if ($this->_hasfiles == true){
			return $this->_files;
		} else {
			return $_FILES;
		}
	}

	public function setRequestTable(array $request)
	{
		$this->_request = $request;
		$this->_hasrequest = true;
	}

	public function getRequestTable(){
		if ($this->_hasrequest == true){
			return $this->_request;
		} else {
			return $_REQUEST;
		}
	}

	public function eraseRequest(){
		if ($this->_hasrequest == true){
			$this->_request = [];
		} else {
			$_REQUEST = [];
		}
	}

	public function setCookieTable(array $cookie)
	{
		$this->_cookie = $cookie;
		$this->_hascookie = true;
	}



	public function setGetTable(array $get)
	{
		$this->_get = $get;
		$this->_hasget = true;
	}

	public function setPostTable(array $post)
	{
		$this->_post = $post;
		$this->_haspost = true;
	}

	public function setServerTable(array $server)
	{
		$this->_server = $server;
		$this->_hasserver = true;
	}

	public function setcookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
	{
		if ($this->_hascookie == true){
			$this->_cookie[$name] = $value;
		} else {
			setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
			$_COOKIE[$name] = $value;
		}
	}

	public function setSession($name, $value = null)
	{
		if ($this->_hassession == true)
			$this->_session[$name] = $value;
		else
			$_SESSION[$name] = $value;
	}

	public function setFiles($name, $value = null)
	{
		if ($this->_hasfiles == true)
			$this->_files[$name] = $value;
		else
			$_FILES[$name] = $value;
	}

	public function setRequest($name, $value = null)
	{
		if ($this->_hasrequest == true)
			$this->_request[$name] = $value;
		else
			$_REQUEST[$name] = $value;
	}

	public function setGet($name, $value = null)
	{
		if ($this->_hasget == true)
			$this->_get[$name] = $value;
		else
			$_GET[$name] = $value;
	}

	public function setPost($name, $value = null)
	{
		if ($this->_haspost == true)
			$this->_post[$name] = $value;
		else
			$_POST[$name] = $value;
	}

	public function setServer($name, $value = null)
	{
		if ($this->_hasserver == true)
			$this->_server[$name] = $value;
		else
			$_SERVER[$name] = $value;
	}


	/**
	 * @return \Harmony2\Router\Route
	 */
	public function getRoute()
	{
		return $this->route;
	}

	public function isAjax()
	{
		return filter_has_var(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')
			&& 'XMLHttpRequest' === filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');
	}

	public function checkSignature($lifetime, $secret)
	{
		// check timestamp validity
		{
			$expires = $this->get('expires');
			$e = new InvalidArgumentException('invalid @expires value', 400);

			if (null === $expires)
				throw $e;

			$now = new DateTime();
			$end = new DateTime();
			$end->setTimestamp((int)$expires);

			if ($end > $now)
				throw $e;

			$now->setTimestamp($now->getTimestamp() - $lifetime);

			if ($end < $now)
				throw $e;
		}

		// check hash validity
		{
			$hash = $this->get('hash');
			$e = new InvalidArgumentException('invalid @hash value', 400);

			if (null === $hash)
				throw $e;

			$properties = array();

			foreach ($this->_request as $name => &$value) {
				if ('hash' !== $name)
					$properties[] = array($name, $value);
			}

			usort($properties, function (&$a, &$b) {
				return strcasecmp($a[0], $b[0]);
			});

			$properties = array_map(function (&$v) {
				return implode('', $v);
			}, $properties);

			if (md5(implode('', $properties) . $secret) !== $hash)
				throw $e;
		}

		return true;
	}

	public function getMethod()
	{
		return filter_has_var(INPUT_SERVER, 'REQUEST_METHOD') ?
			filter_input(INPUT_SERVER, 'REQUEST_METHOD') :
			null;
	}

	public function get($name, $default = null)
	{
		if ($this->_hasget == true)
			return isset($this->_get[$name]) ? $this->_get[$name] : $default;
		else
			return isset($_GET[$name]) ? $_GET[$name] : $default;
	}

	public function post($name, $default = null)
	{
		if ($this->_haspost == true)
			return isset($this->_post[$name]) ? $this->_post[$name] : $default;
		else
			return isset($_POST[$name]) ? $_POST[$name] : $default;
	}

	public function request($name, $default = null)
	{
		if ($this->_hasrequest == true)
			return isset($this->_request[$name]) ? $this->_request[$name] : $default;
		else
			return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
	}

	public function files($name, $default = null)
	{
		if ($this->_hasfiles == true)
			return isset($this->_files[$name]) ? $this->_files[$name] : $default;
		else
			return isset($_FILES[$name]) ? $_FILES[$name] : $default;
	}


	public function server($name, $default = null)
	{
		if ($this->_hasserver == true)
			return isset($this->_server[$name]) ? $this->_server[$name] : $default;
		else
			return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
	}

	public function getAllNameSession()
	{
		if ($this->_hassession == true){
			return array_keys($this->_session);
		} else {
			return array_keys($_SESSION);
		}
	}

	public function session($name, $default = null)
	{
		if ($this->_hassession == true)
			return isset($this->_session[$name]) ? $this->_session[$name] : $default;
		else
			return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;


	}

	public function cookie($name, $default = null)
	{
		if ($this->_hascookie == true)
			return isset($this->_cookie[$name]) ? $this->_cookie[$name] : $default;
		else
			return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
	}


	public function issetGet($name)
	{
		if ($this->_hasget == true)
			return isset($this->_get[$name]);
		else
			return isset($_GET[$name]);
	}

	public function issetPost($name)
	{
		if ($this->_haspost == true)
			return isset($this->_post[$name]);
		else
			return isset($_POST[$name]);
	}

	public function issetRequest($name)
	{
		if ($this->_hasrequest == true)
			return isset($this->_request[$name]);
		else
			return isset($_REQUEST[$name]);
	}

	public function issetFiles($name)
	{
		if ($this->_hasfiles == true)
			return isset($this->_files[$name]);
		else
			return isset($_FILES[$name]);
	}


	public function issetServer($name)
	{
		if ($this->_hasserver == true)
			return isset($this->_server[$name]);
		else
			return isset($_SERVER[$name]);
	}

	public function issetSession($name)
	{
		if ($this->_hassession == true)
			return isset($this->_session[$name]);
		else
			return isset($_SESSION[$name]);
	}

	public function issetCookie($name)
	{
		if ($this->_hascookie == true)
			return isset($this->_cookie[$name]);
		else
			return isset($_COOKIE[$name]);
	}


	public function unsetGet($name)
	{
		if ($this->_hasget == true)
			unset($this->_get[$name]);
		else
			unset($_GET[$name]);
	}

	public function unsetPost($name)
	{
		if ($this->_haspost == true)
			unset($this->_post[$name]);
		else
			unset($_POST[$name]);
	}

	public function unsetRequest($name)
	{
		if ($this->_hasrequest == true)
			unset($this->_request[$name]);
		else
			unset($_REQUEST[$name]);
	}

	public function unsetFiles($name)
	{
		if ($this->_hasfiles == true)
			unset($this->_files[$name]);
		else
			unset($_FILES[$name]);
	}


	public function unsetServer($name)
	{
		if ($this->_hasserver == true)
			unset($this->_server[$name]);
		else
			unset($_SERVER[$name]);
	}

	public function unsetSession($name)
	{
		if ($this->_hassession == true)
			unset($this->_session[$name]);
		else
			unset($_SESSION[$name]);
	}

	public function unsetCookie($name)
	{
		if ($this->_hascookie == true)
			unset($this->_cookie[$name]);
		else
			unset($_COOKIE[$name]);
	}


	public function getBaseURI()
	{
		if ($this->_hasserver ==true) {
			if (false !== $pos = strpos($this->_server['REQUEST_URI'], '?')) {
				return substr($this->_server['REQUEST_URI'], 0, $pos);
			}
			return $this->_server['REQUEST_URI'];
		}
		if (false !== $pos = strpos($_SERVER['REQUEST_URI'], '?')) {
			return substr($_SERVER['REQUEST_URI'], 0, $pos);
		}
		return $_SERVER['REQUEST_URI'];
	}

	public function setRoute(Route $route)
	{
		$this->route = $route;

		return $this;
	}

	public function sessionStart()
	{
		if ($this->_hassession == false) {
			try{
				session_start();
			}catch(\RedisException $e){
				throw new \Exception('Erreur d\'ouverture de session : '.$e->getMessage(),500);
			}
		}
	}

	public function sessionCLose()
	{
		if ($this->_hassession == false) {
			try {
				session_write_close();
			} catch (\RedisException $e) {
			}
		}
	}

	public function addTableSession(...$params)
	{
		if (count($params) < 3)
			return false;

		if ($this->_hassession == true) {
			if (!isset($this->_session[$params[0]]))
				$this->_session[$params[0]] = [];
			$t = &$this->_session[$params[0]];
		} else {
			if (!isset($_SESSION[$params[0]]))
				$_SESSION[$params[0]] = [];
			$t = &$_SESSION[$params[0]];
		}

		for ($i = 1; $i < (count($params) - 1); $i++) {
			if (!isset($t[$params[$i]]))
				$t[$params[$i]] = [];
			$t = &$t[$params[$i]];
		}
		$t = $params[count($params) - 1];
		return true;
	}

	public function delTableSession(...$params)
	{
		if (count($params) < 2)
			return false;

		if ($this->_hassession == true) {
			if (!isset($this->_session[$params[0]]))
				$this->_session[$params[0]] = [];
			$t = &$this->_session[$params[0]];
		} else {
			if (!isset($_SESSION[$params[0]]))
				$_SESSION[$params[0]] = [];
			$t = &$_SESSION[$params[0]];
		}

		for ($i = 1; $i < (count($params) - 1); $i++) {
			if (!isset($t[$params[$i]]))
				$t[$params[$i]] = [];
			$t = &$t[$params[$i]];
		}
		unset($t[$params[count($params) - 1]]);

		return true;
	}

	public function getTableSession(...$params)
	{
		if (count($params) < 2)
			return false;

		if ($this->_hassession == true) {
			if (!isset($this->_session[$params[0]]))
				$this->_session[$params[0]] = [];
			$t = &$this->_session[$params[0]];
		} else {
			if (!isset($_SESSION[$params[0]]))
				$_SESSION[$params[0]] = [];
			$t = &$_SESSION[$params[0]];
		}

		for ($i = 1; $i < (count($params) - 1); $i++) {
			if (!isset($t[$params[$i]]))
				$t[$params[$i]] = [];
			$t = &$t[$params[$i]];
		}
		if (isset($t[$params[count($params) - 1]]))
			return $t[$params[count($params) - 1]];
		else
			return [];
	}

	public function destroySession()
	{
		if ($this->_hassession == true) {
			$this->_session = [];
		} else {
			session_destroy();
		}
	}

}


