<?php
namespace Harmony2\Http;

use Harmony2\Http\Header;
use Harmony2\Http\Response;

class ResponseRedirect implements Response
{
	private $url_redirect;

	public function __construct($url)
	{
		if (count(func_get_args()) > 1)
			throw new \Exception('Number of argument is incorrect');
		if ($url == '')
			throw new \Exception("Redirect url is empty");
		$this->url_redirect = $url;
	}

	public function getUrl()
	{
		return $this->url_redirect;
	}

	public function send( $return = false)
	{
		if ($return == true) {
			return $this->getUrl();
		}
		Header::status(301);
		header('Location: ' . html_entity_decode($this->getUrl()));
		exit();
	}
}