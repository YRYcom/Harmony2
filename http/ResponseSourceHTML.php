<?php
/**
 * Created by Yannick LALLEAU.
 * Date: 02/01/2017
 * Time: 13:55
 */

namespace Harmony2\Http;

use Harmony2\DebugBar;
use Harmony2\Template;

class ResponseSourceHTML implements Response
{
	private $html;

	public function __construct(string $html)
	{
		$this->html = $html;
	}

	/**
	 * @return String
	 */
	public function getHtml()
	{
		return $this->html;
	}

	/**
	 * @param bool $return
	 * @return string|true
	 */
	public function send( $return = false)
	{
		if ($return == true) {
			return $this->html;
		}
		echo $this->html;
		return true;
	}


}