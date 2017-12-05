<?php
/**
 * Created by Yannick LALLEAU.
 * Date: 23/12/2016
 * Time: 14:56
 */

namespace Harmony2\Http;


class ResponseBlankImg implements Response
{


	public function send($return = false)
	{
		if ($return == true)
			return true;

		header('Content-Type: image/png');
		echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');

	}

}