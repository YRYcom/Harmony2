<?php
namespace Harmony2\Http;


class ResponseJSON implements Response
{
	private $data;
	private $options;

	public function __construct($data)
	{
		$this->data = $data;
		$this->options = [];
	}

	public function setOptions($options)
	{
		$this->options = $options;
	}

	public function send($return = false)
	{
		if ($return == true) {
			return [
				'data' => $this->data,
				'options' => $this->options
			];
		}
		header('Content-Type: application/json', true);
		if (!empty($this->options))
			echo json_encode($this->data, $this->options);
		else
			echo json_encode($this->data);
	}
}