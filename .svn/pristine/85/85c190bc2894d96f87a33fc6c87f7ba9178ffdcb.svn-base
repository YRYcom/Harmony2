<?php
namespace Harmony2\Http;

use Harmony2\DebugBar;
use Harmony2\Template;

class ResponseImage implements Response
{
	private $mime_type;

	private $image;


	public function __construct()
	{
		$this->image = '';
		$this->mime_type = '';
	}

	public function setImage($image){
		$this->image = $image;
	}


	public function setMimeTypeWithExtension($extension)
	{
		switch ($extension) {
			case "png":
				$mime_type = "image/png";
				break;
			case "gif":
				$mime_type = "image/gif";
				break;
			default:
				$mime_type = "image/jpeg";
				break;
		}
		return ($this->mime_type = $mime_type);
	}

	public function send( $return = false)
	{

		if ($return == true) {

			return [
				'mime_type' => $this->mime_type,
				'image' => $this->image
			];
		}

		header("Content-type: " . $this->mime_type);
		echo $this->image;

		return true;
	}


}