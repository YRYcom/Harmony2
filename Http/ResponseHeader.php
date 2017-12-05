<?php
namespace Harmony2\Http;


class ResponseHeader implements Response
{
	private $code;
  private $message;

	/**
	 * ResponseHeader constructor.
	 * @param int $code
	 */
	public function __construct($code, $message='')
	{
		$this->code = $code;
    $this->message = $message;
	}

	/**
	 * @param $code
	 * @return $this
	 */
	public function setCode($code)
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCode()
	{
		return $this->code;
	}

  public function getMessage()
  {
    return $this->message;
  }

	public function send($return = false)
	{
		if ($return == true)
			return $this->getCode();
		Header::status($this->getCode(), false, $this->getMessage());
		return true;
	}

}