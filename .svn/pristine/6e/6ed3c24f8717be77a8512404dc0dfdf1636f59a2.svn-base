<?php
/**
 * Created by PhpStorm.
 * User: yannick
 * Date: 23/05/2016
 * Time: 12:09
 */

namespace Harmony2;


use Harmony2\Http\Request;


class Session
{

	/** @var  Request $_request */
	protected static $_request = null;

	public static function start(Request $request)
	{
		self::$_request = $request;
		self::$_request->sessionStart();
		self::eraseFlash();
	}

	public static function close(){
		self::$_request->sessionClose();
	}

	public static function setFlash($key, $value = true)
	{
		if (is_null(self::$_request))
			throw new \Exception('Session is not init');
		$keys = explode('.', $key);
		if (count($keys) == 1) {
			self::$_request->addTableSession('currentFlash', $keys[0], $value);
			self::$_request->addTableSession('olderFlash', $keys[0], $value);
		}
		if (count($keys) == 2) {
			self::$_request->addTableSession('currentFlash', $keys[0], $keys[1], $value);
			self::$_request->addTableSession('olderFlash', $keys[0], $keys[1], $value);
		}
		if (count($keys) == 3) {
			self::$_request->addTableSession('currentFlash', $keys[0], $keys[1], $keys[2], $value);
			self::$_request->addTableSession('olderFlash', $keys[0], $keys[1], $keys[2], $value);
		}
	}

	public static function delFlash($key)
	{
		if (is_null(self::$_request))
			throw new \Exception('Session is not init');
		$keys = explode('.', $key);
		if (count($keys) == 1) {
			self::$_request->delTableSession('currentFlash', $keys[0]);
			self::$_request->delTableSession('olderFlash', $keys[0]);
		}
		if (count($keys) == 2) {
			self::$_request->delTableSession('currentFlash', $keys[0], $keys[1]);
			self::$_request->delTableSession('olderFlash', $keys[0], $keys[1]);
		}
		if (count($keys) == 3) {
			self::$_request->delTableSession('currentFlash', $keys[0], $keys[1], $keys[2]);
			self::$_request->delTableSession('olderFlash', $keys[0], $keys[1], $keys[2]);
		}
	}

	/**
	 * @param $key
	 * @return array
	 * @throws \Exception
	 */
	public static function getFlash($key)
	{
		if (is_null(self::$_request))
			throw new \Exception('Session is not init');
		$keys = explode('.', $key);

		$sessionName = 'olderFlash';

		if (count($keys) == 1)
			return self::$_request->getTableSession($sessionName, $keys[0]);
		elseif (count($keys) == 2)
			return self::$_request->getTableSession($sessionName, $keys[0], $keys[1]);
		elseif (count($keys) == 3)
			return self::$_request->getTableSession($sessionName, $keys[0], $keys[1], $keys[2]);
		else
			return [];

	}

	public static function eraseFlash()
	{
		if (is_null(self::$_request))
			throw new \Exception('Session is not init');
		self::$_request->session('currentFlash', []);
	}
}